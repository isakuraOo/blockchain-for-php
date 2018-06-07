<?php
/**
 * @author iSakura <i@joosie.cn>
 */
namespace Joosie\Blockchain;

use Joosie\Blockchain\Exceptions\BlockchainAccountException;
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
     * @var \Joosie\Blockchain\AccountResourceInterface
     */
    protected $resource;

    public function init()
    {
        $config = $this->blockchainManager->config;

        // 私钥文件被配置时载入私钥数据
        if (!empty($config['privateKeyPath'])) {
            // $fp = fopen($config['privateKeyPath'], 'r');
            // $key = fread($fp, 8192);
            // fclose($fp);
            // $this->setPrivateKey(openssl_get_privatekey($key, ''));
            $this->setPrivateKey(file_get_contents($config['privateKeyPath']));
        }

        // 公钥文件被配置时载入公钥数据
        if (!empty($config['publicKeyPath'])) {
            $this->setPublicKey(file_get_contents($config['publicKeyPath']));
        }
    }

    /**
     * 创建一个新的钱包账户
     * @return Joosie\Blockchain\Account
     */
    public function create()
    {
        // 创建新的密钥
        $res = openssl_pkey_new([
            'private_key_bits'  => 4096,
            'private_key_type'  => OPENSSL_KEYTYPE_RSA
        ]);
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
     * 设置数据
     * @param array $options
     */
    public function set(array $options)
    {
        foreach ($options as $name => $value) {
            if (!isset($this->$name)) {
                throw new BlockchainAccountException(sprintf('Invalid attribute: %s', $name));
            }
            $this->$name = $value;
        }
        return $this;
    }

    /**
     * 设置私钥
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * 设置公钥
     * @param mixed $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function validate()
    {

    }

    /**
     * 是否已有账号
     * 根据私钥是否为空来判断是否拥有账号
     * @return boolean
     */
    public function hasAccount()
    {
        return !empty($this->privateKey);
    }

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
            // 'resourceInfo'  => $this->resource->getInfo(),
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
     * 根据公钥生成钱包地址
     * @return string
     */
    public function getAddressByPublic($publicKey = null)
    {
        $publicKey = $publicKey ?: $this->publicKey;
        if (empty($publicKey)) {
            throw new BlockchainAccountException('You have\'t public key');
        }

        // 将公钥进行两次哈希加密 ripemd160(sha256(PUBLIC_KEY)) 后
        // 在头部拼接上主链版本号
        $tmpAddress = sprintf('%s%s', $this->blockchainManager->config['version'],
            hash('ripemd160', hash('sha256', $publicKey))
        );

        // 根据上一步得到的哈希值进行校验码的计算并拼接到尾部
        $address = sprintf('%s%s', $tmpAddress,
            $this->generateAddressVerifyCode($tmpAddress)
        );
        
        // 最后将拼接了版本号、校验位的内容进行一次 sha256 哈希得到最终地址
        return sprintf('0x%s', hash('sha256', $address));
    }

    /**
     * 将私钥数据保存到文件
     * @param  string $filename 目标文件
     * @return boolean
     */
    public function savePrivateKeyToFile($filename)
    {
        return $this->saveToFile($filename, $this->privateKey);
    }

    /**
     * 将公钥数据保存到文件
     * @param  string $filename 目标文件
     * @return boolean
     */
    public function savePublicKeyToFile($filename)
    {
        return $this->saveToFile($filename, $this->publicKey);
    }

    /**
     * 将数据保存到文件
     * @param  string $filename 目标文件
     * @param  mixed  $data     数据内容
     * @return bollean
     */
    public function saveToFile($filename, $data)
    {
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            throw new BlockchainAccountException(sprintf('Dir no exist: [%s]', $dir));
        }

        return file_put_contents($filename, $data) !== false;
    }

    /**
     * 根据临时地址计算校验位字符串
     * 对地址进行两次 sha256 哈希计算后得到的前八位十六进制字符
     * @param  String $tmpAddress 通过公钥计算出的带有主链版本号的地址
     * @return String
     */
    protected function generateAddressVerifyCode($tmpAddress)
    {
        return substr(hash('sha256', hash('sha256', $tmpAddress)), 0, 8);
    }

    public function __get($name)
    {
        return isset($this->{$name}) ? $this->{$name} : null;
    }
}