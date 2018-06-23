<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Helper\Data;
use Joosie\Blockchain\Exceptions\BlockchainBlockException;
use Joosie\Blockchain\Exceptions\BlockchainAccountException;
use Joosie\Blockchain\Helper\Log;
use Joosie\Blockchain\Helper\Date;
use Joosie\Blockchain\Event\EventType;

/**
 * 区块链新区块类
 */
class NewBlock extends Block
{
    // 单一区块最大交易数据存储数量
    const MAX_TRANSACTION_DATA_NUM  = 1000;

    /**
     * 用于判断是否有其它节点率先完成新区块的计算工作
     * 默认 false, 通过 Server 监听服务（状态机）控制
     * @var boolean
     */
    protected $otherNodeCompleted = false;

    /**
     * 开始进行新区块哈希计算
     * @return void
     */
    public function startWork()
    {
        $this->beforeStartWork();

        // 计算耗时
        $start = microtime(true);

        $i = 0;
        Log::t(sprintf('Current attempts: %d', $i));
        Log::t(sprintf('Time consumed: %s', Date::timeFormat('0.0000')));
        Log::t(sprintf('Current memory usage: %s', memory_get_usage()));

        // 利用一个随机初始值来波动每个节点的计算耗时
        $this->nonce = rand(0, 1000000);
        while (!$this->generateBlockHash()->validate()) {
            $nowTime = microtime(true);
            Log::t(sprintf("\033[3ACurrent attempts: %d", ++$i));
            Log::t(sprintf('Time consumed: %s', Date::timeFormat(bcsub($nowTime, $start, 4))));
            Log::t(sprintf('Current memory usage: %s', memory_get_usage()));

            // 其它节点率先完成计算
            if ($this->otherNodeCompleted) {
                return;
            }

            usleep(66);
        }

        // 如果哈希值生成成功，开始抢占区块管理权
        $this->seizeManagementRights();
        
        $this->afterStartWork();
    }

    /**
     * 添加一条交易数据
     * @param string $data 一条交易数据
     */
    public function addOneTransaction($data)
    {
        if (count($this->transactionData) >= self::MAX_TRANSACTION_DATA_NUM) {
            throw new BlockchainBlockException(
                'Too much transaction num in one block!',
                BlockchainBlockException::ERR_TOO_MUCH_TRANSACTION_NUM
            );
        }
        
        $data = !is_string($data) ? json_encode($data) : $data;
        array_push($this->transactionData, $data);
        return $this;
    }

    /**
     * 刷新区块需要存储的交易数据
     * @return void
     */
    protected function refreshTransactionData()
    {
        $store = $this->blockchainManager->store;
        $this->transactionData = $store->getNoConfirmTransactions();
        $this->dataHash = $this->generateDataHash();
    }

    /**
     * 设置上一个区块的哈希值
     * @param string $prevHash
     */
    public function setPrevHash($prevHash)
    {
        $this->prevHash = $prevHash;
        return $this;
    }

    /**
     * 设置新区块难度系数
     * 使用哈希值前导 0 数量作为难度系数
     * @param integer $difficulty
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    /**
     * 抢占区块所有权
     * @return void
     */
    public function seizeManagementRights()
    {
        $account = $this->blockchainManager->account;
        $privateKey = $account->privateKey;
        if (empty($privateKey)) {
            throw new BlockchainAccountException('Invalid private key!');
        }

        $this->blockNumber = $this->blockNumber ?: $this->getNewBlockNumber();
        $this->belongtoAccount = $this->belongtoAccount ?: $this->blockchainManager->account->getMyAccountAddress();
        // 使用用户私钥签名数据   
        $data['block'] = $this->getBlockData();
        $data['sign'] = Data::privateEncode(json_encode($data), $privateKey);
        $data['publicKey'] = Data::base58encode($account->publicKey);

        // 广播新区块数据
        $this->blockchainManager->sockServer->sendto(json_encode($data));
        Log::t(sprintf("New block data: \n%s", json_encode($data['block'], JSON_PRETTY_PRINT)), Log::LOG_TYPE_SUCCESS);

        // 区块数据写入区块链
        if (!$this->pushToBlockchain()) {
            throw new BlockchainBlockException(
                sprintf('Insert block data is fail! Block hash: [%s]', $this->hash)
            );
        }
    }

    /**
     * 停止新区块计算工作
     * @return void
     */
    public function stopWork()
    {
        $this->otherNodeCompleted = true;
    }

    /**
     * 生成区块哈希值
     * @return self
     */
    protected function generateBlockHash()
    {
        $this->version = $this->version ?: $this->blockchainManager->config['version'];
        $this->timestamp = time();
        $this->dataHash = $this->dataHash ?: $this->generateDataHash();

        $data = $this->getHeader();
        unset($data['hash']);
        unset($data['blockNumber']);

        $tmpStr = '';
        foreach ($data as $k => $v) {
            $tmpStr .= $k . '=' . $v . '&';
        }
        $this->hash = '0x' . hash('sha256', hash('sha256', trim($tmpStr, '&')));
        $this->nonce++;
        return $this;
    }

    /**
     * 有效性验证
     * @return Boolean
     */
    protected function validate()
    {
        return $this->blockchainManager->consensus->validate($this);
    }

    /**
     * 新区块开始正式计算前的准备操作
     * @return void
     */
    protected function beforeStartWork()
    {
        $this->checkStartWork();
        $this->otherNodeCompleted = false;

        // 监听事件
        $this->blockchainManager->event->listen(
            EventType::EVENT_OTHER_NODE_CREATE_BLOCK_SUCC,
            [$this, 'stopWork']
        );
        $this->blockchainManager->event->listen(
            EventType::EVENT_HAS_NEW_TRANSACTION_DATA,
            [$this, 'refreshTransactionData']
        );

        // 初始化区块需要保存的区块体数据
        $this->refreshTransactionData();
    }

    /**
     * 新区块计算完毕后的后续操作
     * @return void
     */
    protected function afterStartWork()
    {
        // 成功被区块存储的交易记录需要修改为已确认
        $this->blockchainManager->store->confirmTransactions($this->transactionData);
        // 继续进行下一个区块的计算工作
        Log::t('Start generate next one block...');
        $this->readyNewBlock()->startWork();
    }

    /**
     * 新区块计算的初始校验
     * @return void
     */
    protected function checkStartWork()
    {
        if (is_null($this->prevHash)) {
            throw new BlockchainBlockException('Invalid block attribute: [prevHash]');
        } elseif (is_null($this->difficulty)) {
            throw new BlockchainBlockException('Invalid block attribute: [difficulty]');
        }
    }

    /**
     * 计算区块体数据的摘要哈希值
     * 计算采用 Merkle Tree 算法取根节点值作为最终的哈希值
     * @return Array
     */
    protected function generateDataHash($data = [])
    {
        // 如果传入数据为空时，根据区块体交易数据计算 Merkle Tree 的
        // 叶子节点哈希值列表
        if (empty($data)) {
            foreach ($this->transactionData as $value) {
                if (!is_string($value)) {
                    throw new BlockchainBlockException('Invalid transaction type!');
                }
                $data[]['value'] = hash('sha256', $value);
            }
        }

        // @see getParentHashListForMerkleTree()
        $result = $this->getParentHashListForMerkleTree($data);

        // 未计算到根节点
        if (count($result) > 1) {
            return $this->generateDataHash($result);
        }

        $this->merkleTreeData = $result;
        return '0x' . $result[0]['value'];
    }

    /**
     * 计算上一层哈希列表
     * 相邻节点计算父节点哈希值
     * 最后一个没有相邻节点时，使用自身值拷贝进行计算父节点哈希
     * @param  array $data 当前层级数据列表
     * 每个节点的数据结构
     * [
     *     'value' => '哈希值',
     *     'leftChildNode' => 左子节点,
     *     'rightChildNode' => 右子节点,
     * ]
     * @return array
     */
    protected function getParentHashListForMerkleTree($data)
    {
        if (empty($data)) {
            return [['value' => hash('sha256', '')]];
        }

        $result = [];
        $i = 0;
        while ($i < count($data)) {
            $tmpValue = isset($data[$i+1]['value'])
                ? $data[$i]['value'] . $data[$i+1]['value']
                : $data[$i]['value'] . $data[$i]['value'];

            $result[] = [
                'value'             => hash('sha256', $tmpValue),
                'leftChildNode'     => $data[$i],
                'rightChildNode'    => isset($data[$i+1]) ? $data[$i+1] : null,
            ];

            $i += 2;
        }
        return $result;
    }

    /**
     * 获取当前新区块编号
     * @return integer
     */
    protected function getNewBlockNumber()
    {
        return $this->blockchainManager->store->getBlockchainLenght() + 1;
    }

    /**
     * 向区块链尾部添加一个区块
     * @return boolean
     */
    protected function pushToBlockchain()
    {
        $block = $this->getBlockData();
        return $this->blockchainManager->store->insertOneBlock(
            $this->hash, json_encode($block)
        );
    }
}