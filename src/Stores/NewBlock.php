<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain\Stores;

use Joosie\Blockchain\Helper\Data;
use Joosie\Blockchain\Exceptions\BlockchainBlockException;
use Joosie\Blockchain\Exceptions\BlockchainAccountException;

/**
 * 区块链新区块类
 */
class NewBlock extends Block
{
    const DEFAULT_DIFFICULTY = 3;

    /**
     * 添加一条交易数据
     * @param String $data 一条交易数据
     */
    public function add(string $data)
    {
        if (is_null($this->transactionData)) {
            $tmpData = json_encode([$data]);
        } else {
            $tmpData = json_decode($this->transactionData, true);
            array_push($tmpData, $data);
        }

        $this->transactionData = $tmpData;
        return $this;
    }

    /**
     * 开始进行新区块哈希计算
     * @return void
     */
    public function startWork()
    {
        $this->beforeStartWork();

        $start = microtime(true);
        // 利用一个随机初始值来波动每个节点的计算耗时
        $this->nonce = rand(0, 1000000);
        while (!$this->generateBlockHash()->validate()) {
            usleep(666);

            # TODO 事件触发停止计算
        }
        echo sprintf("耗时：%f\n", microtime(true) - $start);

        // 如果哈希值生成成功，开始抢占区块管理权
        if (!empty($this->hash)) {
            $this->seizeManagementRights();
        }
        
        $this->afterStartWork();
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
        $privateKey = $this->blockchainManager->account->privateKey;
        if (empty($privateKey)) {
            throw new BlockchainAccountException('Invalid private key!');
        }

        // 使用用户私钥对区块头数据加密，openssl 进行二次加密将
        $blockHeadersEncrypted = Data::privateEncode(
            json_encode($this->getHeaders()), $privateKey
        );
        var_dump($blockHeadersEncrypted);


        # TODO 等待并校验承认数据有效性的节点数量是否过半
    }

    /**
     * 生成区块哈希值
     * @return self
     */
    protected function generateBlockHash()
    {
        $this->version = $this->version ?: $this->blockchainManager->config['version'];
        $this->dataHash = $this->dataHash ?: $this->generateDataHash();
        $this->timestamp = time();

        $data = $this->getHeaders();
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
        for ($i = 0; $i < $this->difficulty; $i++) {
            if ($this->hash[$i + 2] !== '0') {
                $this->hash = '';
                return false;
            }
        }
        return true;
    }

    /**
     * 新区块开始正式计算前的准备操作
     * @return void
     */
    protected function beforeStartWork()
    {
        $this->checkStartWork();
    }

    /**
     * 新区块计算完毕后的后续操作
     * @return void
     */
    protected function afterStartWork()
    {
        # TODO after work end
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
            foreach ($this->getTransactionData() as $value) {
                $data[] = hash('sha256', $value);
            }
        }

        // @see getParentHashListForMerkleTree()
        $result = $this->getParentHashListForMerkleTree($data);

        // 未计算到根节点
        if (count($result) > 1) {
            $result = $this->generateMerkleTreeHash($result);
        }
        return $result[0];
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
            return [hash('sha256', '')];
        }

        $result = [];
        for ($i = 0; $i < count($data); $i + 2) {
            if (!isset($data[$i + 1])) {
                // 单数节点
                $result[] = [
                    'value'             => hash('sha256', $data[$i]['value'] . $data[$i]['value']),
                    'leftChildNode'     => $data[$i],
                    'rightChildNode'    => null,
                ];
            } else {
                // 双数节点
                $result[] = [
                    'value'             => hash('sha256', $data[$i]['value'] . $data[$i + 1]['value']),
                    'leftChildNode'     => $data[$i],
                    'rightChildNode'    => $data[$i + 1],
                ];
            }
        }
        return $result;
    }
}