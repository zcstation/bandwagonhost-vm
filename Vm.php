<?php
/**
 * User: https://github.com/zcstation
 * Date: 2018/1/31
 * Time: 14:35
 */

class Vm
{
    /**
     * @var object $instance 存储单例对象
     */
    private static $instance;
    /**
     * @var int $veId 搬瓦工给的VEID
     */
    private $_veId;

    /**
     * @var string $apiKey 搬瓦工上的API KEY
     */
    private $_apiKey;

    /**
     * @var string $url 搬瓦工api调用地址的基本（不完全的）url
     */
    private $_url = 'https://api.64clouds.com/v1/';

    /**
     * BWG constructor，设置veid，api_key和url参数
     *
     * @param $veId int 搬瓦工veid
     * @param $apiKey string 搬瓦工api_key
     */
    private function __construct ( $veId, $apiKey)
    {
        $veId = isset($veId) ? $veId : null;

        if (empty($veId)) {

            exit('请先设置 VEID');

        }

        $apiKey = isset($apiKey) ? $apiKey : null;

        if (empty($apiKey)) {

            exit('请先设置 API KEY');

        }

        $this->_veId = $veId;

        $this->_apiKey = $apiKey;
    }

    /**
     * 连接单例，返回单例对象
     *
     * @param $veId int 搬瓦工veid
     * @param $apiKey string 搬瓦工api_key
     *
     * @return Vm|object
     */
    public static function instance ( $veId, $apiKey)
    {
        if (!(self::$instance instanceof self)) {

            self::$instance = new self($veId, $apiKey);

        }

        return self::$instance;
    }

    /**
     * curl模拟http的post请求
     *
     * @param string $url 请求的url
     * @param mixed $data post提交的参数
     * @param array $header header
     *
     * @return  string  返回的结果
     */
    private function postHttp($url, $data, $header = array('Content-type:application/json;charset=utf-8'))
    {
        // 初始化curl
        $ch = curl_init();
        // 设置要抓取的url地址等相应选项
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header头
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 是否显示抓取到的数据,1为不显示
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 设置post方法，1表示表示使用post
        curl_setopt($ch, CURLOPT_POST, 1);
        // 设置post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // 执行抓取
        $content = curl_exec($ch);
        // 关闭curl
        curl_close($ch);
        // 返回抓取到的数据
        return $content;
    }

    /**
     * curl模拟http的get请求
     *
     * @param string $url 请求的url地址
     * @param array $header header头
     *
     * @return  string  返回的结果
     */
    private function getHttp($url, $header = array('Content-type:application/json;charset=utf-8'))
    {
        // 初始化curl
        $ch = curl_init();
        // 设置要抓取的url地址，header等相应选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 是否显示抓取到的数据,1为不显示
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 设置header代理
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 执行抓取
        $content = curl_exec($ch);
        // 关闭curl
        curl_close($ch);
        // 返回抓取到的数据
        return $content;
    }

    /**
     * 拼接完整的请求url，并返回拼接后的url
     *
     * @param $param string 请求方法
     *
     * @return string
     */
    private function buildUrl ( $param )
    {
        if (empty($param)) {

            exit('请先设置 请求方法');

        }

        $url = $this->_url . $param . '?veid=' . $this->_veId . '&api_key=' . $this->_apiKey;

        return $url;
    }

    /**
     * 拼接并请求无需参数的api，返回服务器返回的结果
     * 如果问我为什么不用__call？强迫症不想看到ide的警告而已
     *
     * @param $name string 请求方法，不带参数
     * @param boolean $array 是否使用数组
     * @return object|array
     */
    private function getWithoutData ( $name , $array = false )
    {
        $url = $this->buildUrl($name);

        $result = $this->getHttp($url);

        $jsonObj = json_decode($result, $array);

        return $jsonObj;
    }

    /**
     * 停止vps，关机
     *
     * @return object
     */
    public function stop ()
    {
        return $this->getWithoutData('stop');
    }

    /**
     * 启动vps，开机
     *
     * @return object
     */
    public function start ()
    {
        return $this->getWithoutData('start');
    }

    /**
     * 重启vps
     *
     * @return object
     */
    public function restart ()
    {
        return $this->getWithoutData('restart');
    }

    /**
     * 允许强制停止被卡住（不能以正常方式停止）的VPS。请谨慎使用此功能，因为任何未保存的数据将丢失
     *
     * @return object
     */
    public function kill ()
    {
        return $this->getWithoutData('kill');
    }

    /**
     * 获取服务器的基本信息，不含启动时的状态等信息
     *
     * @return object
     */
    public function getServiceInfo ()
    {
        return $this->getWithoutData('getServiceInfo');
    }

    /**
     * 此函数返回getServiceInfo提供的所有数据。此外，它还提供了VPS的详细状态。
     * 请注意，此方法可能需要15秒才能完成
     *
     * @return object
     */
    public function getLiveServiceInfo ()
    {
        return $this->getWithoutData('getLiveServiceInfo');
    }

    /**
     * 获取操作系统名称和支持列表
     *
     * @return object
     */
    public function getAvailableOS ()
    {
        return $this->getWithoutData('getAvailableOS');
    }

    /**
     * 检索与服务暂停相关的信息
     *
     * @return object
     */
    public function getSuspensionDetails ()
    {
        return $this->getWithoutData('getSuspensionDetails');
    }

    /**
     * 当你在短时间内执行过多的API调用，服务器可能会忽略你的请求
     * 使用这个方法可以监控这个问题
     *
     * @return object
     */
    public function getRateLimitStatus ()
    {
        return $this->getWithoutData('getRateLimitStatus');
    }

    /**
     * 清除由RECORD_ID识别的滥用问题，并取消暂停的VPS
     * 有关详细信息，请参阅getSuspensionDetails调用
     *
     * @return object
     */
    public function unsuspend ()
    {
        return $this->getWithoutData('unsuspend');
    }

    /**
     * （仅限OVZ）克隆远程服务器或VPS
     *
     * @param string $ip 远程ip地址
     * @param string $password 远程服务器密码
     * @param int $ssh 远程连接端口
     *
     * @return object
     */
    public function cloneFromExternalServer ($ip, $password, $ssh = 22)
    {
        $data = array(
            'externalServerIP' => $ip,
            'externalServerSSHport' => $ssh,
            'externalServerRootPassword' => $password
        );

        return $this->getWithData('cloneFromExternalServer', $data);
    }

    /**
     * 执行带有请求参数的操作
     *
     * @param $name string 请求方法
     * @param $data mixed 请求参数
     *
     * @return mixed
     */
    private function getWithData ( $name, $data )
    {
		/*
		// 此处也可以使用post请求
		
		$url = $this->buildUrl($name);
		
		$result = $this->postHttp($url, $data);
		*/
        $url = $this->buildUrl($name) . '&' . http_build_query($data);

        $result = $this->getHttp($url);

        $jsonObj = json_decode($result);

        return $jsonObj;
    }

    /**
     * 启动VPS迁移到新的位置。获取新的位置ID作为输入
     *
     * @param $location string 新位置的id
     *
     * @return mixed
     */
    public function migrateStart ( $location )
    {
        $data = array(
            'location' => $location
        );

        return $this->getWithData('migrate/start', $data);
    }

    /**
     * 返回所有可能的迁移位置
     *
     * @return object
     */
    public function migrateGetLocations ()
    {
        return $this->getWithoutData('migrate/getLocations');
    }

    /**
     * 释放指定的IPv6的地址
     *
     * @param $ip string 要释放的ipv6地址
     *
     * @return mixed
     */
    public function ipv6Delete ( $ip )
    {
        $data = array(
            'ip' => $ip
        );

        return $this->getWithData('ipv6/delete', $data);
    }

    /**
     * 分配一个新的IPv6地址。对于初始IPv6分配，需要空IP（无参数调用）
     * 并自动分配来自可用池的新IP
     * 所有后续请求的IPv6地址必须在第一个IPv6地址的/ 64子网内
     *
     * @param mixed $ip IPv6地址
     *
     * @return object
     */
    public function ipv6Add ( $ip = null )
    {
        if (is_null($ip)) {

            return $this->getWithoutData('ipv6/add');

        }

        $data = array(
            'ip' => $ip
        );

        return $this->getWithData('ipv6/add', $data);
    }

    /**
     * 从由VEID和令牌标识的另一个实例导入快照
     * 必须先通过"snapshotExport"调用从另一个实例获取VEID和令牌
     *
     * @param $sourceVeid string 源veid
     * @param $sourceToken string 源token
     *
     * @return mixed
     */
    public function snapshotImport ( $sourceVeid, $sourceToken )
    {
        $data = array(
            'sourceVeid' => $sourceVeid,
            'sourceToken' => $sourceToken
        );

        return $this->getWithData('snapshot/import', $data);
    }

    /**
     * 生成可以将快照转移到另一个实例的令牌
     *
     * @param $snapshot string 快照名称
     *
     * @return mixed
     */
    public function snapshotExport ( $snapshot )
    {
        $data = array(
            'snapshot' => $snapshot
        );

        return $this->getWithData('snapshot/export', $data);
    }

    /**
     * 设置或删除sticky属性（“sticky”快照永远不会被清除）
     * 可以使用“snapshotList”调用检索快照的名称
     *
     * @param $snapshot string 快照名称
     * @param $sticky int sticky = 1 设置sticky属性, sticky = 0 删除粘性属性
     * @return mixed
     */
    public function snapshotToggleSticky ( $snapshot, $sticky)
    {
        $data = array(
            'snapshot' => $snapshot,
            'sticky' => $sticky
        );

        return $this->getWithData('snapshot/toggleSticky', $data);
    }

    /**
     * 通过文件名恢复快照（可以使用“snapshotList”调用检索）
     * 这将覆盖VPS上的所有数据
     *
     * @param $snapshot string 快照名称
     *
     * @return mixed
     */
    public function snapshotRestore ( $snapshot )
    {
        $data = array(
            'snapshot' => $snapshot
        );

        return $this->getWithData('snapshot/restore', $data);
    }

    /**
     * 通过文件名删除快照（可以使用“snapshotList”调用检索）
     *
     * @param $snapshot
     * @return mixed
     */
    public function snapshotDelete ( $snapshot )
    {
        $data = array(
            'snapshot' => $snapshot
        );

        return $this->getWithData('snapshot/delete', $data);
    }

    /**
     * 获取所有快照列表
     *
     * @param boolean $array 是否使用数组
     * @return object|array
     */
    public function snapshotList ($array = false)
    {
        return $this->getWithoutData('snapshot/list', $array);
    }

    /**
     * 创建快照
     *
     * @param $description string 快照描述
     *
     * @return mixed
     */
    public function snapshotCreate ( $description )
    {
        $data = array(
            'description' => $description
        );

        return $this->getWithData('snapshot/create', $data);
    }

    /**
     * 在VPS上（异步）执行一个shell脚本
     *
     * @param $script string shell脚本内容
     *
     * @return mixed
     */
    public function shellScriptExec ( $script )
    {
        $data = array(
            'script' => $script
        );

        return $this->getWithData('shellScript/exec', $data);
    }

    /**
     * 在VPS上（同步）执行shell命令
     *
     * @param $command string shell命令
     *
     * @return mixed
     */
    public function basicShellExec ( $command )
    {
        $data = array(
            'command' => $command
        );

        return $this->getWithData('basicShell/exec', $data);
    }

    /**
     * 模拟VPS目录的更改。可以用来构建一个基础的shell。
     *
     * @param $currentDir string 当前目录
     * @param $newDir string 新目录
     *
     * @return mixed
     */
    public function basicShellCd ( $currentDir, $newDir )
    {
        $data = array(
            'currentDir' => $currentDir,
            'newDir' => $newDir
        );

        return $this->getWithData('basicShell/cd', $data);
    }

    /**
     * 为IP设置新的PTR（RDN的）记录
     *
     * @param $ip string 要设置ptr的ip
     * @param $ptr string 设置的ptr记录
     *
     * @return mixed
     */
    public function setPTR ( $ip, $ptr )
    {
        $data = array(
            'ip' => $ip,
            'ptr' => $ptr
        );

        return $this->getWithData('setPTR', $data);
    }

    /**
     * 设置新的主机名
     *
     * @param $newName string 新的主机名
     *
     * @return mixed
     */
    public function setHostname ( $newName )
    {
        $data = array(
            'newHostname' => $newName
        );

        return $this->getWithData('setHostname', $data);
    }

    /**
     * 返回二维数组，详细的使用统计信息
     *
     * @return object
     */
    public function getRawUsageStats ()
    {
        return $this->getWithoutData('getRawUsageStats');
    }

    /**
     * 返回二维数组，详细的使用统计信息，已废弃，同getRawUsageStats
     *
     * @return object
     */
    public function getUsageGraphs ()
    {
        return $this->getWithoutData('getRawUsageStats');
    }

    /**
     * 生成并设置新的root密码
     *
     * @return object
     */
    public function resetRootPassword ()
    {
        return $this->getWithoutData('resetRootPassword');
    }

    /**
     * 重新安装操作系统。必须通过“os”变量指定操作系统
     * 使用getAvailableOS调用获取可用系统的列表
     *
     * @param $os string 操作系统名称
     *
     * @return mixed
     */
    public function reinstallOS ( $os )
    {
        $data = array(
            'os' => $os
        );

        return $this->getWithData('reinstallOS', $data);
    }
}
