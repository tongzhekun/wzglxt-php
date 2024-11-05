<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Request;

class Index extends BaseController
{
    public function index()
    {
        return '<style>*{ padding: 0; margin: 0; }</style><iframe src="https://www.thinkphp.cn/welcome?version=' . \think\facade\App::version() . '" width="100%" height="100%" frameborder="0" scrolling="auto"></iframe>';
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }

    public function checkLogin()
    {
        $userId = Request::param('userId');
        $password = Request::param('password');
        $user = Db::table('user')->where('user_id', $userId)->find();
        $passwordBack=$user['password'];
        $statusBack=$user['status'];
        if($statusBack==='2'){
            $data=[];
            $data['message'] = '用户被禁用，请联系管理员';
            return json(['data' => $data, 'code' =>300]);
        }else{
            if (password_verify($password, $passwordBack)) {
                 //----------记录操作START---------------
                $sys_record_data = [
                    'user_id' =>  $user['user_id'],
                    'type' => '登录',
                    'time' => date('Y-m-d H:i:s')
                ];
                Db::table('sysrecord')->insert($sys_record_data);
                $data=[];
                $data['userId'] = $user['user_id'];
                $data['username'] = $user['username'];;
                $data['message'] = 'success';
                $data['detail'] = '登录成功';
                // return json($data);
                return json(['data' => $data, 'code' =>200]);
            } else {
                $data=[];
                $data['message'] = '用户名或密码错误';
                return json(['data' => $data, 'code' =>300]);
            }
        }
    }


    public function register()
    {
        $username = Request::param('username');
        $password = Request::param('password');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = Request::param('userId');
        $register_data = [
            'username' =>  $username,
            'password' => $hashedPassword,
            'user_id' => $userId
        ];
        Db::table('user')->insert($register_data);
        $user = Db::table('user')->where('user_id', $userId)->find();
        if ($user != null) {
            
            //----------记录操作START---------------
            $sys_record_data = [
                'user_id' =>  $user['user_id'],
                'type' => '注册',
                'time' => date('Y-m-d H:i:s')
            ];
            Db::table('sysrecord')->insert($sys_record_data);
            $data=[];
            $data['userId'] = $user['user_id'];
            $data['username'] = $user['username'];;
            $data['message'] = 'success';
            $data['detail'] = '注册成功';
            // return json($data);
            return json(['data' => $data, 'code' =>200]);
        } else {
            $data=[];
            $data['message'] = '注册失败';
            // return json($data);
            return json(['data' => $data, 'code' =>300]);
        }
    }

    public function uploadTobacco()
    {
        DB::table('tobacco_quantity_temp')->where("1=1")->delete();
        header('Content-Type: application/json');
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        // 检查接收到的数据是否包含所需字段
        if (isset($data['skus'])  && is_array($data['skus'])) {
            $skus = $data['skus'];
            $isValid = true;
    
            // 遍历每个 SKU 项目
            foreach ($skus as $item) {
                // 验证 stock 是否为数字
                if (isset($item['sku'], $item['name'], $item['stock']) && is_numeric($item['stock']) && $item['sku']!== '' && $item['name']!== '' && $item['stock']!== '') {
                    // 逐行插入数据
                    try {
                        Db::table('tobacco_quantity_temp')->insert([
                            'sku' => $item['sku'],
                            'name' => $item['name'],
                            'stock' => (int)$item['stock'],
                            'time' => date('Y-m-d'), // 使用当前时间
                        ]);
                    } catch (Exception $e) {
                        // 记录插入错误
                        return json(['data' => ['message' => "Insert failed for SKU: " . $item['sku']], 'code' => 300]);
                    }
                } else {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                // 成功响应
                return json(['data' => ['message' => '所有卷烟参数上传成功'], 'code' => 200]);
            } else {
                return json(['data' => ['message' => '表格数据填写有误，请检查'], 'code' => 300]);
            }
        } else {
            return json(['data' => ['message' => '请求数据格式不正确'], 'code' => 300]);
        }
    }


    public function dowloadTobacco()
    {
        $tempList = Db::table('tobacco_quantity_temp')->select();
        $data = [];
        $data['list'] = []; 
        if ($tempList == null || count($tempList) === 0) {
            $data['message'] = '下载成功';
            return json(['data' => $data, 'code' => 200]);
        } else {
            foreach ($tempList as $item) {
                // 假设 $item 是一个对象
                $data['list'][] = [
                    'sku' => isset($item['sku'])? $item['sku'] : null, 
                    'name' => isset($item['name'])? $item['name'] : null, 
                    'stock' => isset($item['stock'])? $item['stock'] : null, 
                ];
            }
            $data['message'] = '下载成功';
            return json(['data' => $data, 'code' => 200]);
        }
    }

    public function uploadCust()
    {
        DB::table('cust_temp')->where("1=1")->delete();
        header('Content-Type: application/json');
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        // 检查接收到的数据是否包含所需字段
        if (isset($data['customers']) && is_array($data['customers']) && count($data['customers']) === 30) {
            $customers = $data['customers'];
            $isValid = true;
            // 生成 gearList 数组，包含从 1 到 30 的数字
            $gearList = range(1, 30);
            // 遍历 customers 数组，并根据索引插入对应的 gear
            foreach ($customers as $index => $item) {
                // 验证 customer 是否为数字
                if (isset($item) && is_numeric($item) && $item !== '') {
                    try {
                        DB::table('cust_temp')->insert([
                            'num' => $item,
                            'gear' => $gearList[$index], // 使用与当前 customer 索引对应的 gear 值
                            'time' => date('Y-m-d'), // 使用当前时间
                        ]);
                    } catch (Exception $e) {
                        // 记录插入错误
                        return json(['data' => ['message' => "Insert failed for num: " . $item . ", gear: " . $gearList[$index]], 'code' => 300]);
                    }
                } else {
                    $isValid = false;
                    break;
                }
            }
    
            if ($isValid) {
                // 成功响应
                return json(['data' => ['message' => '所有客户参数上传成功'], 'code' => 200]);
            } else {
                return json(['data' => ['message' => '表格数据填写有误，请检查'], 'code' => 300]);
            }
        } else {
            return json(['data' => ['message' => '请求数据格式不正确或 customers 数组长度不为 30'], 'code' => 300]);
        }
    }


    public function dowloadCust()
    {
        $tempList = Db::table('cust_temp')->select();
        $data = [];
        $data['list'] = []; 
        if ($tempList == null || count($tempList) === 0) {
            $data['message'] = '下载成功';
            return json(['data' => $data, 'code' => 200]);
        } else {
            foreach ($tempList as $item) {
                // 假设 $item 是一个对象
                $data['list'][] = [
                    'gear' => isset($item['gear'])? $item['gear'] : null, 
                    'num' => isset($item['num'])? $item['num'] : null, 
                ];
            }
            $data['message'] = 'success';
            return json(['data' => $data, 'code' => 200]);
        }
    }
    public function download()
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="template.xlsx"');
        // 这里假设模板文件为 template.xlsx，可以根据实际情况修改
        readfile('template.xlsx');
    }
}
