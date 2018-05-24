<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainAccoutException;
use Joosie\Blockchain\Providers\Service;

/**
 * 区块链账号身份处理类
 */
class Account extends Service implements AccountInterface
{
    /**
     * 私钥
     * @var string
     */
    protected $privateKey = '';

    /**
     * 公钥
     * @var string
     */
    protected $publicKey = '';

    /**
     * 账户钱包地址
     * @var string
     */
    protected $address = '';

    /**
     * 钱包账户财产资源实例
     * @var null
     */
    protected $resource = null;

    /**
     * 获取我的钱包地址
     * @return string
     */
    public function getMyAccountAddress()
    {
        if (empty($this->address) && !empty($this->publicKey)) {
            return $this->getAddressByPublic();
        }
        return $this->address;
    }

    /**
     * 获取我的账户信息
     * 目前直接返回密钥跟钱包地址，调用方直接保存使用
     * 后续升级扩展「助记词」功能
     * @return array
     */
    public function getMyAccountInfo()
    {
        $address = !empty($this->address) ? $this->address : $this->getAddressByPublic();
        return [
            'privateKey'    => $this->privateKey,
            'publicKey'     => $this->publicKey,
            'address'       => $address,
            'resourceInfo'  => $this->resource->getInfo(),
        ];
    }

    /**
     * 转账
     * @param  string $toAddress   目标钱包地址
     * @param  float  $resourceNum 转账资源数量
     * @return boolean
     */
    public function transfer($toAddress, $resourceNum)
    {

    }

    /**
     * 创建一个新的钱包账户
     * @return Joosie\Blockchain\Account
     */
    public function create()
    {
        // 创建新的密钥
        $res = openssl_pkey_new(['private_key_bits' => 512]);
        // 提取私钥
        openssl_pkey_export($res, $this->privateKey);
        // 生成公钥
        $tmpArr = openssl_pkey_get_details($res);
        if ($tmpArr === false) {
            return false;
        }
        $this->publicKey = $tmpArr['key'];
        // 生成钱包地址
        $this->address = $this->getAddressByPublic();
        return $this;
    }

    /**
     * 根据公钥生成钱包地址
     * @return string
     */
    private function getAddressByPublic()
    {
        if (empty($this->publicKey)) {
            throw new BlockchainAccoutException('You have\'t public key');
        }
        return hash('ripemd160', hash('sha256', $this->publicKey));
    }
}