<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

interface AccountInterface
{

    /**
     * 创建账号
     */
    public function create();

    /**
     * 设置账号信息
     * @param array $options 配置参数
     */
    public function set(array $options);

    /**
     * 设置私钥内容
     * @param String $privateKey
     */
    public function setPrivateKey($privateKey);

    /**
     * 设置公钥内容
     * @param String $publicKey
     */
    public function setPublicKey($publicKey);

    /**
     * 账号合法性验证
     */
    public function validate();

    /**
     * 是否已有账号
     * @return boolean
     */
    public function hasAccount();

    /**
     * 获取我的账户地址
     */
    public function getMyAccountAddress();

    /**
     * 获取我的账户详情
     */
    public function getMyAccountInfo();

    /**
     * 转账
     * @param  string $toAddress   目标账户地址
     * @param  float  $resourceNum 资源数量
     */
    public function transfer($toAddress, $resourceNum);

}