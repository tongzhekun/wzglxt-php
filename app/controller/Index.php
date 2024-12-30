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
    //判断用户登录
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
    //判断用户密码
    public function editPassWord()
    {
        $fields = Request::param('elForm');
        $userId = Request::param('userId');
        $password=$fields['fields'][0]['fieldValue'];
        $newpassword=$fields['fields'][1]['fieldValue'];
        $user = Db::table('user')->where('user_id', $userId)->find();
        $passwordBack=$user['password'];
        $statusBack=$user['status'];
        if($statusBack==='2'){
            $data=[];
            $data['message'] = '用户被禁用，请联系管理员';
            return json(['data' => $data, 'code' =>300]);
        }else{
            if (password_verify($password, $passwordBack)) {
                $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
                Db::table('user')
                ->where('user_id',$userId)
                ->update([
                    'password' => $hashedPassword
                ]);
                $sys_record_data = [
                    'user_id' =>  $userId,
                    'type' => '密码修改',
                    'time' => date('Y-m-d H:i:s')
                ];
                Db::table('sysrecord')->insert($sys_record_data);
                return json(['data' =>[], 'message' => '密码修改成功', 'code' =>200]);
            } else {
                return json(['data' => [], 'code' => 300, 'message' => '旧密码错误，请重新输入']);
            }
        }
        return json(['data' =>[], 'code' =>300]);
    }
    

    //注册用户
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

                    if (isset($item['sku'], $item['name'], $item['weekSalesNum'],
                    $item['fixRate'],$item['businessStock'],$item['stockSaleRate'],
                    $item['fixSaleRate'],$item['societyStock'],$item['societySales'],$item['societySalesDate'])){
                        // 逐行插入数据
                        $weekSalesNum=(float)$item['weekSalesNum'];  // 向下四舍五入
                        $fixRate=(float)$item['fixRate'];
                        $businessStock=(float)$item['businessStock'];
                        $stockSaleRate=(float)$item['stockSaleRate'];
                        $fixSaleRate=(float)$item['fixSaleRate'];
                        $societyStock=(float)$item['societyStock'];
                        $societySales=(float)$item['societySales'];
                        try {
                            Db::table('tobacco_quantity_temp')->insert([
                                'sku' => $item['sku'],
                                'name' => $item['name'],
                                'week_sales_num'=> floor($weekSalesNum * 100) / 100, // 向下四舍五入,
                                'fix_rate' => floor($fixRate * 100) / 100, // 向下四舍五入,
                                'business_stock' => floor($businessStock * 100) / 100,  // 向下四舍五入,
                                'stock_sale_rate'=> floor($stockSaleRate * 100) / 100, // 向下四舍五入,
                                'fix_sale_rate' => floor($fixSaleRate * 100) / 100, // 向下四舍五入,
                                'society_stock' => floor($societyStock * 100) / 100, // 向下四舍五入,
                                'society_sales' => floor($societySales * 100) / 100, // 向下四舍五入,
                                'society_sales_date' => $item['societySalesDate'],
                                'time' => date('Y-m-d')// 使用当前时间
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
                    return json(['data' => ['message' => '烟草参数表格数据填写有误，请检查'], 'code' => 300]);
                }
            } else {
                return json(['data' => ['message' => '请求烟草参数数据格式不正确'], 'code' => 300]);
            }
        }


        public function downloadTobacco()
        {
            $tempList = Db::table('tobacco_quantity_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '下载烟草参数成功';
                return json(['data' => $data, 'code' => 200]);
            } else {
                foreach ($tempList as $item) {
                    // 假设 $item 是一个对象
                    $data['list'][] = [
                        'sku' => isset($item['sku'])? $item['sku'] : null,
                        'name' => isset($item['name'])? $item['name'] : null,
                        'weekSalesNum' => isset($item['week_sales_num'])? $item['week_sales_num'] : null,
                        'fixRate' => isset($item['fix_rate'])? $item['fix_rate'] : null,
                        'businessStock' => isset($item['business_stock'])? $item['business_stock'] : null,
                        'stockSaleRate' => isset($item['stock_sale_rate'])? $item['stock_sale_rate'] : null,
                        'fixSaleRate' => isset($item['fix_sale_rate'])? $item['fix_sale_rate'] : null,
                        'societyStock' => isset($item['society_stock'])? $item['society_stock'] : null,
                        'societySales' => isset($item['society_sales'])? $item['society_sales'] : null,
                        'societySalesDate' => isset($item['society_sales_date'])? $item['society_sales_date'] : null

                    ];
                }
                $data['message'] = '下载烟草参数成功';
                return json(['data' => $data, 'code' => 200]);
            }
        }

      public function queryTobacco()
       {
            $tempList = Db::table('tobacco_quantity_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '烟草参数数据表无数据，查询失败';
                return json(['data' => $data, 'code' => 999]);
            } else {
                foreach ($tempList as $item) {

                    $data['list'][] = [
                        'sku' => isset($item['sku'])? $item['sku'] : null,
                        'name' => isset($item['name'])? $item['name'] : null,
                        'weekSalesNum' => isset($item['week_sales_num'])? $item['week_sales_num'] : null,
                        'fixRate' => isset($item['fix_rate'])? $item['fix_rate'] : null,
                        'businessStock' => isset($item['business_stock'])? $item['business_stock'] : null,
                        'stockSaleRate' => isset($item['stock_sale_rate'])? $item['stock_sale_rate'] : null,
                        'fixSaleRate' => isset($item['fix_sale_rate'])? $item['fix_sale_rate'] : null,
                        'societyStock' => isset($item['society_stock'])? $item['society_stock'] : null,
                        'societySales' => isset($item['society_sales'])? $item['society_sales'] : null
                    ];
                }
                 $data['message'] = '查询烟草参数成功';
               return json(['data' => $data, 'code' => 200]);
            }
       }

       public function queryTobaccoBySku()
          {
               header('Content-Type: application/json');
               $input = file_get_contents('php://input');
               $req = json_decode($input, true);
               if (isset($req['sku'])) {
                    $tempList = Db::table('tobacco_quantity_temp')
                    ->where('sku',$req['sku'])
                    ->select();
                }else{
                    return json(['data' => null, 'code' => 999]);
                }
               $data = [];
               $data['list'] = [];
               if ($tempList == null || count($tempList) === 0) {
                   $data['message'] = '烟草参数数据表无数据，查询失败';
                   return json(['data' => $data, 'code' => 999]);
               } else {
                   foreach ($tempList as $item) {

                       $data['list'][] = [
                           'sku' => isset($item['sku'])? $item['sku'] : null,
                           'name' => isset($item['name'])? $item['name'] : null,
                           'weekSalesNum' => isset($item['week_sales_num'])? $item['week_sales_num'] : null,
                           'fixRate' => isset($item['fix_rate'])? $item['fix_rate'] : null,
                           'businessStock' => isset($item['business_stock'])? $item['business_stock'] : null,
                           'stockSaleRate' => isset($item['stock_sale_rate'])? $item['stock_sale_rate'] : null,
                           'fixSaleRate' => isset($item['fix_sale_rate'])? $item['fix_sale_rate'] : null,
                           'societyStock' => isset($item['society_stock'])? $item['society_stock'] : null,
                           'societySales' => isset($item['society_sales'])? $item['society_sales'] : null,
                           'societySalesDate' => isset($item['society_sales_date'])? $item['society_sales_date'] : null
                       ];
                   }
                    $data['message'] = '查询烟草参数成功';
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
                foreach ($customers as $item) {
                    // 验证 customer 是否为数字
                    if (isset($item['gear'],$item['num']) && is_numeric($item['gear'])&&is_numeric($item['num'])) {
                        try {
                            DB::table('cust_temp')->insert([
                            'gear' => (int)$item['gear'], // 使用与当前 customer 索引对应的 gear 值
                             'num' => (int)$item['num'],
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
                    return json(['data' => ['message' => '客户参数表格数据填写有误，请检查'], 'code' => 300]);
                }
            } else {
                return json(['data' => ['message' => '客户参数请求数据格式不正确或 customers 数组长度不为 30'], 'code' => 300]);
            }
        }


        public function downloadCust()
        {
            $tempList = Db::table('cust_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '下载客户参数成功';
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

       public function queryCust()
       {
            $tempList = Db::table('cust_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '客户参数数据表无数据，查询失败';
                return json(['data' => $data, 'code' => 999]);
            } else {
                foreach ($tempList as $item) {
                    $data['list'][] = [
                         'gear' => isset($item['gear'])? $item['gear'] : null,
                          'num' => isset($item['num'])? $item['num'] : null,
                    ];
                }
                 $data['message'] = '查询客户参数成功';
               return json(['data' => $data, 'code' => 200]);
            }
       }

       public function uploadDecayRule()
       {
            DB::table('decay_rule_temp')->where("1=1")->delete();
            header('Content-Type: application/json');
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            // 检查接收到的数据是否包含所需字段
            if (isset($data['decayRules'])  && is_array($data['decayRules'])) {
                $decayRules = $data['decayRules'];
                $isValid = true;
                // 遍历每个 SKU 项目
                foreach ($decayRules as $item) {
                    if (isset($item['stocks'],$item['lev30Num'], $item['decayRate']) &&
                        $item['stocks']!== '' && $item['lev30Num']!== '' && $item['decayRate']!== '' ) {
                        // 逐行插入数据
                        try {
                            Db::table('decay_rule_temp')->insert([
                                'stock_min_max' => $item['stocks'],
                                'level_30_num' => (int)$item['lev30Num'],
                                'decay_rate' => (float)$item['decayRate'],
                                 'remark' => $item['remark'],
                                'update_date' => date('Y-m-d'), // 使用当前时间
                            ]);
                        } catch (Exception $e) {
                            // 记录插入错误
                            return json(['data' => ['message' => "Insert failed for decayRules: " . $item['stocks']], 'code' => 300]);
                        }
                    } else {
                        $isValid = false;
                        break;
                    }
                }
                if ($isValid) {
                    // 成功响应
                    return json(['data' => ['message' => '所有速率价格参数上传成功'], 'code' => 200]);
                } else {
                    return json(['data' => ['message' => '表格数据填写有误，请检查'], 'code' => 300]);
                }
            } else {
                return json(['data' => ['message' => '请求数据格式不正确'], 'code' => 300]);
            }
       }


        public function downloadDecayRule()
        {
            $tempList = Db::table('decay_rule_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '下载成功';
                return json(['data' => $data, 'code' => 200]);
            } else {
                foreach ($tempList as $item) {
                    // 假设 $item 是一个对象
                    $data['list'][] = [
                        'stocks' => isset($item['stock_min_max'])? $item['stock_min_max'] : null,
                        'lev30Num' => isset($item['level_30_num'])? $item['level_30_num'] : null,
                        'decayRate' => isset($item['decay_rate'])? $item['decay_rate'] : null,
                        'remark' => isset($item['remark'])? $item['remark'] : null,
                    ];
                }
                $data['message'] = '下载成功';
                return json(['data' => $data, 'code' => 200]);
            }
        }


        public function queryDecayRules()
       {
            $tempList = Db::table('decay_rule_temp')->select();
            $data = [];
            $data['list'] = [];
            if ($tempList == null || count($tempList) === 0) {
                $data['message'] = '数据表无数据，查询失败';
                return json(['data' => $data, 'code' => 999]);
            } else {
                foreach ($tempList as $item) {
                  $stocks = isset($item['stock_min_max'])? explode('-', $item['stock_min_max']) : null; // 将 "0-30" 解析为数组 [0, 30]
                    // 假设 $item 是一个对象
                    $data['list'][] = [
                        'stocks' => array_map('intval', $stocks),  // 转换为整数数组
                        'lev30Num' => isset($item['level_30_num'])? $item['level_30_num'] : null,
                        'decayRate' => isset($item['decay_rate'])? $item['decay_rate'] : null,
                    ];
                }
                 $data['message'] = '查询成功';
               return json(['data' => $data, 'code' => 200]);
            }
       }


      public function saveCalculateDistribution()
         {
             header('Content-Type: application/json');
             $input = file_get_contents('php://input');
             $data = json_decode($input, true);

             // 检查接收到的数据是否包含所需字段
             if (isset($data['distributions'])  && is_array($data['distributions'])) {
                 $decayRules = $data['distributions'];
                 $isValid = true;
                 // 遍历每个 SKU 项目
                  $serialId=$decayRules[0]['serialId'];
                   if (isset($serialId)) {
                      $count = Db::table('calculate_distribution')
                                 ->where('serialId', '20241108')
                                 ->count();
                   }else{
                       return json(['data' => null, 'code' => 999]);
                   }
                  if($count==0){
                     foreach ($decayRules as $item) {
                         if (isset($item['serialId'],$item['sku'],$item['cigName'],$item['stocksSale'], $item['isSale'], $item['stocksPre'],$item['allocations']) )
                         {
                             // 逐行插入数据
                             try {
                                 Db::table('calculate_distribution')->insert([
                                     'serialId' => $item['serialId'],
                                     'sku' => $item['sku'],
                                     'cig_name' => $item['cigName'],
                                     'stocks_sale' => (float)$item['stocksSale'],
                                     'is_sale' => (float)$item['isSale'],
                                     'stocks_pre' => (float)$item['stocksPre'],
                                    'level_30_num' => (int)$item['allocations'][29],
                                    'level_29_num' => (int)$item['allocations'][28],
                                    'level_28_num' => (int)$item['allocations'][27],
                                    'level_27_num' => (int)$item['allocations'][26],
                                    'level_26_num' => (int)$item['allocations'][25],
                                    'level_25_num' => (int)$item['allocations'][24],
                                    'level_24_num' => (int)$item['allocations'][23],
                                    'level_23_num' => (int)$item['allocations'][22],
                                    'level_22_num' => (int)$item['allocations'][21],
                                    'level_21_num' => (int)$item['allocations'][20],
                                    'level_20_num' => (int)$item['allocations'][19],
                                    'level_19_num' => (int)$item['allocations'][18],
                                    'level_18_num' => (int)$item['allocations'][17],
                                    'level_17_num' => (int)$item['allocations'][16],
                                    'level_16_num' => (int)$item['allocations'][15],
                                    'level_15_num' => (int)$item['allocations'][14],
                                    'level_14_num' => (int)$item['allocations'][13],
                                    'level_13_num' => (int)$item['allocations'][12],
                                    'level_12_num' => (int)$item['allocations'][11],
                                    'level_11_num' => (int)$item['allocations'][10],
                                    'level_10_num' => (int)$item['allocations'][9],
                                    'level_9_num' => (int)$item['allocations'][8],
                                    'level_8_num' => (int)$item['allocations'][7],
                                    'level_7_num' => (int)$item['allocations'][6],
                                    'level_6_num' => (int)$item['allocations'][5],
                                    'level_5_num' => (int)$item['allocations'][4],
                                    'level_4_num' => (int)$item['allocations'][3],
                                    'level_3_num' => (int)$item['allocations'][2],
                                    'level_2_num' => (int)$item['allocations'][1],
                                    'level_1_num' => (int)$item['allocations'][0],
                                     'update_date' => date('Y-m-d'), // 使用当前时间
                                 ]);

                             } catch (Exception $e) {
                                 // 记录插入错误
                                 return json(['data' => ['message' => "Insert failed for decayRules: " . $item['serialId']], 'code' => 9999]);
                             }
                         } else {
                             $isValid = false;
                             break;
                         }
                     }
                 }
                 if ($isValid) {
                     // 成功响应
                     return json(['data' => ['message' => '数据插入成功'], 'code' => 200]);
                 } else {
                     return json(['data' => ['message' => '数据插入失败，请检查'], 'code' => 9999]);
                 }
             } else {
                 return json(['data' => ['message' => '数据插入失败'], 'code' => 300]);
             }
         }

      public function queryCalculateDistributionBySerialId()
       {
            header('Content-Type: application/json');
            $input = file_get_contents('php://input');
            $req = json_decode($input, true);
            if (isset($req['serialId'])) {
                 $tempList = Db::table('calculate_distribution')
                 ->where('serialId',$req['serialId'])
                 ->select();
             }else{
                 return json(['data' => null, 'code' => 999]);
             }
             $data = [];
             $data['list'] = [];
             if ($tempList == null || count($tempList) === 0) {
                 $data['message'] = '数据表无数据，查询失败';
                 return json(['data' => $data, 'code' => 999]);
             } else {
                 foreach ($tempList as $item) {
                     $data['list'][] = [
                        'sku' => isset($item['sku'])? $item['sku'] : null,
                        'serialId' => isset($item['serialId'])? $item['serialId'] : null,
                        'cigName' => isset($item['cig_name'])? $item['cig_name'] : null,
                        'stocksSale' => isset($item['stocks_sale'])? $item['stocks_sale'] : null,
                        'isSale' => isset($item['is_sale'])? $item['is_sale'] : null,
                        'stocksPre' => isset($item['stocks_pre'])? $item['stocks_pre'] : null,
                        'allocations'=>[
                             $item['level_30_num'],
                             $item['level_29_num'],
                             $item['level_28_num'],
                             $item['level_27_num'],
                             $item['level_26_num'],
                             $item['level_25_num'],
                             $item['level_24_num'],
                             $item['level_23_num'],
                             $item['level_22_num'],
                             $item['level_21_num'],
                             $item['level_20_num'],
                             $item['level_19_num'],
                             $item['level_18_num'],
                             $item['level_17_num'],
                             $item['level_16_num'],
                             $item['level_15_num'],
                             $item['level_14_num'],
                             $item['level_13_num'],
                             $item['level_12_num'],
                             $item['level_11_num'],
                             $item['level_10_num'],
                             $item['level_9_num'],
                             $item['level_8_num'],
                             $item['level_7_num'],
                             $item['level_6_num'],
                             $item['level_5_num'],
                             $item['level_4_num'],
                             $item['level_3_num'],
                            $item['level_2_num'],
                            $item['level_1_num'],
                        ]
                    ];
                 }
                  $data['message'] = '查询成功';
                return json(['data' => $data, 'code' => 200]);
             }
       }



        public function savePeriod()
        {
            header('Content-Type: application/json');
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            // 检查接收到的数据是否包含所需字段
            if (isset($data['period']) ) {
                    $item=$data['period'];
                    if (isset($item['serialId'],$item['periodName'])) {
                        try {
                          //判断该批次是否已经存在
                            $tempList = Db::table('cigarette_manage_serialId')
                                       ->where('serialId',$item['serialId'])
                                       ->select();
                              //不存在时插入，存在不做处理
                             if( $tempList ==null){
                               DB::table('cigarette_manage_serialId')->insert([
                                       'serialId' => $item['serialId'], //
                                        'period_name' => $item['periodName'],
                                        'time' => date('Y-m-d')// 使用当前时间
                                       ]);
                             }
                        return json(['data' => ['message' => '数据插入成功'], 'code' => 200]);

                        } catch (Exception $e) {
                            // 记录插入错误
                            return json(['data' => ['message' => "Insert failed for num: " . $item . ", serialId: " .$item['serialId'] ], 'code' => 300]);
                        }
                    } else {
                      return json(['data' => ['message' => '传入参数字段有误'], 'code' =>9999]);
                    }
            } else {
                return json(['data' => ['message' => '传入参数有误'], 'code' => 9999]);
            }
        }
       public function queryPeriod()
              {
                   $tempList = Db::table('cigarette_manage_serialId')
                    ->select();
                    $data = [];
                   $data['list'] = [];
                   if ($tempList == null || count($tempList) === 0) {
                       $data['message'] = '无周期数据数据，查询失败';
                       return json(['data' => $data, 'code' => 999]);
                   } else {
                       foreach ($tempList as $item) {

                           $data['list'][] = [
                               'serialId' => isset($item['serialId'])? $item['serialId'] : null,
                               'periodName' => isset($item['period_name'])? $item['period_name'] : null
                           ];
                       }
                        $data['message'] = '查询批次成功';
                      return json(['data' => $data, 'code' => 200]);
                   }
              }
       public function queryPeriodBySerialId()
            {
                 header('Content-Type: application/json');
                 $input = file_get_contents('php://input');
                 $req = json_decode($input, true);
                 if (isset($req['serialId'])) {
                      $tempList = Db::table('cigarette_manage_serialId')
                      ->where('serialId',$req['serialId'])
                      ->select();
                  }else{
                      return json(['data' => null, 'code' => 999]);
                  }
                 $data = [];
                 $data['list'] = [];
                 if ($tempList == null || count($tempList) === 0) {
                     $data['message'] = '历史周期批次表无数据，查询失败';
                     return json(['data' => $data, 'code' => 999]);
                 } else {
                     foreach ($tempList as $item) {

                         $data['list'][] = [
                             'serialId' => isset($item['serialId'])? $item['serialId'] : null,
                             'periodName' => isset($item['period_name'])? $item['period_name'] : null
                         ];
                     }
                      $data['message'] = '查询批次成功';
                    return json(['data' => $data, 'code' => 200]);
                 }
            }
    
    //机构树
    public function tree()
    {   
        $inst_code = Request::param('inst_code');
        $sql = "select * from inst where inst_code=? 
        union all 
        select * from inst where inst_code in (select inst_code  from inst where up_inst_code=?)
         union all 
        select * from inst where inst_code in (select inst_code from inst where up_inst_code in (select inst_code  from inst where up_inst_code=?))";
        $institutions= Db::query($sql, [$inst_code,$inst_code,$inst_code]);
        // 构建机构树的函数
        function buildInstitutionTree($institutions) {
            $inst_code = Request::param('inst_code');
            $tree = [];
            foreach ($institutions as $institution) {
                if ($institution["inst_code"] == $inst_code) {
                    // 找到根节点，添加到树中并递归构建子节点
                    $tree[] = [
                        "label" => $institution["inst_name"],
                        "value" => $institution["inst_code"],
                        "children" => buildSubInstitutions($institutions, $institution["inst_code"])
                    ];
                }
            }
            return $tree;
        }
        // 构建下属机构的函数
        function buildSubInstitutions($institutions, $parentCode) {
            $subInstitutions = [];
            foreach ($institutions as $institution) {
                if ($institution["up_inst_code"] == $parentCode) {
                    $subInstitutions[] = [
                        "label" => $institution["inst_name"],
                        "value" => $institution["inst_code"],
                        "children" => buildSubInstitutions($institutions, $institution["inst_code"])
                    ];
                }
            }
            return $subInstitutions;
        }
        // 构建机构树
        $institutionTree = buildInstitutionTree($institutions);
        return json(['data' => $institutionTree, 'code' => 200]);
    }
    //查询用户角色
    public function userRole()
    {
        $userId = Request::param('userId');
        $searchList = Db::table('user_role')
        ->where('user_id', $userId)
        ->select();
        if ($searchList == null || count($searchList) === 0) {
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //重置密码
    public function resetPassWord()
    {
        $userId = Request::param('userId');
        $userIdOperate = Request::param('userIdOperate');
        $newpassword='123456';
        $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
        $result=Db::table('user')->where('user_id',$userId)->select();
        if ($result == null || count($result) === 0) {
            return json(['message' => '当前用户未注册，请先注册', 'code' => 300]);
        } else {
            Db::table('user')
            ->where('user_id',$userId)
            ->update([
                'password' => $hashedPassword
            ]);
            $sys_record_data = [
                'user_id' =>  $userIdOperate,
                'type' => '密码重置'.$userId,
                'time' => date('Y-m-d H:i:s'),
                'remark' => '重置用户'.$userId.'的密码'
            ];
            Db::table('sysrecord')->insert($sys_record_data);
            return json(['message' => '密码重置成功', 'code' => 200]);
        }
    }
    //查询所有用户
    public function searchUser()
    { 
        $userId = Request::param('userId'); 
        $instCode = Request::param('instCode'); 
        $roleId = Request::param('roleId'); 
        $userName = Request::param('userName'); 
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList=Db::table('employee');
        $searchList1=Db::table('employee');
        if (!empty($userId)) { 
            $searchList = $searchList->where('employee_code', $userId);
            $searchList1 = $searchList1->where('employee_code', $userId); 
        }
        if (!empty($userName)) { 
        
            $searchList = $searchList->where('employee_name', 'like', '%' . $userName . '%');
            $searchList1 = $searchList1->where('employee_name', 'like', '%' . $userName . '%'); 
        }
        if (!empty($instCode)) { 
            $searchList = $searchList->where('inst_code', $instCode);
            $searchList1 = $searchList1->where('inst_code', $instCode); 
        }
        if (!empty($roleId)) { 
            $searchList2 = Db::table('user_role')->where('role_id', $roleId)->select();
            $searchList2Array = $searchList2->toArray();
            $userIds = array_column($searchList2Array, 'user_id');
            $searchList = $searchList->where('employee_code', 'in', $userIds);
            $searchList1 = $searchList1->where('employee_code', 'in', $userIds);
        }
        $searchList=$searchList->order('status')->page($page, $pageSize)->select();
        $total = $searchList1->count();
        if ($searchList == null || count($searchList) === 0) {
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    //查询所有角色
    public function searchRole()
    {
        $searchList=Db::table('role')->select();
        if ($searchList == null || count($searchList) === 0) {
            return json(['data' => [], 'code' => 200]);
        } else {
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //查询用户基本信息
    public function userMessage()
    {
        $userId = Request::param('userId');
        $searchList=Db::table('employee')->where('employee_code',  $userId);
        $searchList =  $searchList
        ->leftJoin('user_role', 'employee.employee_code = user_role.user_id')
        ->leftJoin('role', 'role.role_id = user_role.role_id')
        ->field(['employee.*', 'role.role_id', 'role.role_name', 'role.remark'])
        ->select();
        if ($searchList == null || count($searchList) === 0) {
            return json(['data' => [], 'code' => 200]);
        } else {
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //提交用户信息
    public function submitUserMessage()
    {
        $loanData = Request::param('loanData');
        $telephone = Request::param('telephone');
        $type = Request::param('type');
        $user_name = Request::param('userName');
        $status = Request::param('status');
        $inst_code = Request::param('instCode');
        $user_id = Request::param('userId');
        $userIdOperate = Request::param('userIdOperate');
        $instResult=Db::table('inst')
            ->where('inst_code',$inst_code)->find();
        $inst_name=$instResult['inst_name'];
        try {
            Db::table('employee')
            ->where('employee_code',$user_id)
            ->update([
                'status' => $status,
                'type' => $type,
                'telephone' => $telephone,
                'inst_code' => $inst_code,
                'inst_name' => $inst_name,
            ]);
        } catch (Exception $e) {
            return json(['message' =>'更新用户基础信息失败', 'code' => 300]);
        }
        try {
            DB::table('user_role')
            ->where('user_id', $user_id)
            ->delete();
        } catch (Exception $e) {
            return json(['message' =>'更新用户角色信息删除失败', 'code' => 300]);
        }
        foreach ($loanData as $item) {
            try {
                Db::table('user_role')->insert([
                    'user_id' => $user_id,
                    'role_id' => $item['role_id']
                ]);
            } catch (Exception $e) {
                return json(['message' =>'更新用户角色信息插入失败', 'code' => 300]);
            }
        }
        $sys_record_data = [
            'user_id' =>  $userIdOperate,
            'type' => '用户信息修改',
            'time' => date('Y-m-d H:i:s'),
            'remark' =>'操作修改了'.$user_id.'用户信息',
        ];
        Db::table('sysrecord')->insert($sys_record_data);
        return json(['message' =>'修改用户信息成功', 'code' => 200]);
    }
    //查询现有库存记录
    public function searchCk()
    {
        $instCode = Request::param('instCode');
        $materialName = Request::param('materialName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList = Db::table('materialinfo')
        ->where('inst_code', $instCode)
        ->where('history_type', '0')
        ->where('material_name', 'like', '%' . $materialName . '%')
        ->page($page, $pageSize)->select();
        $total =  Db::table('materialinfo')->where('inst_code', $instCode)
        ->where('history_type', '0')->where('material_name', 'like', '%' . $materialName . '%')
        ->count();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    //删除现有库存记录
    public function deleteCk()
    {
        $instCode = Request::param('instCode');
        $materialCode = Request::param('materialCode');
        try {
            DB::table('materialinfo')
            ->where('inst_code', $instCode)
            ->where('material_code', $materialCode)
            ->delete();
            return json(['message' =>'删除成功', 'code' => 200]);
        } catch (Exception $e) {
            // 记录插入错误
            return json(['data' => ['message' => "删除失败"], 'code' => 300]);
        }
    }
    //导出现有库存记录
    public function exportCk()
    {
        $instCode = Request::param('instCode');
        $materialName = Request::param('materialName');
        $searchList = Db::table('materialinfo')->where('inst_code', $instCode)->where('history_type', '0')->where('material_name', 'like', '%' . $materialName . '%')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //市场部数组
    public function treeSc()
    {
            $inst_code = Request::param('inst_code');
            if (!empty($inst_code)) { 
                $instSql='select inst_code from inst where up_inst_code=? union all select inst_code from inst where up_inst_code in (select inst_code from inst where up_inst_code=? and inst_code IS NOT NULL) and up_inst_code IS NOT NULL';
                $instResult= Db::query($instSql, [$inst_code, $inst_code]);
                $instCodeArray = array_column($instResult, 'inst_code');
                $inst_code_str = "'". implode("', '", $instCodeArray)."'";
                if(strlen($inst_code) === 7){
                    $sql = "select * from inst where LENGTH(inst_code) = 7 and inst_code =? order by inst_code"; 
                    $result= Db::query($sql, [$inst_code]);
                    if ($result == null || count($result) === 0) {
                        $data['message'] = 'success';
                        return json(['data' => [], 'code' => 200]);
                    } else {
                        $data['message'] = 'success';
                        return json(['data' => $result, 'code' => 200]);
                    }
                }else if(strlen($inst_code) === 6){
                    $sql = "select * from inst where LENGTH(inst_code) = 7 and inst_code in (".$inst_code_str.")  order by inst_code"; 
                    $result= Db::query($sql);
                    if ($result == null || count($result) === 0) {
                        $data['message'] = 'success';
                        return json(['data' => [], 'code' => 200]);
                    } else {
                        $data['message'] = 'success';
                        return json(['data' => $result, 'code' => 200]);
                    }
                }
            }else{
                $sql = "select * from inst where LENGTH(inst_code) = 7  order by inst_code"; 
                $result= Db::query($sql);
                if ($result == null || count($result) === 0) {
                    $data['message'] = 'success';
                    return json(['data' => [], 'code' => 200]);
                } else {
                    $data['message'] = 'success';
                    return json(['data' => $result, 'code' => 200]);
                }
            }
    }
    //某一市场部的客户经理下拉
    public function empSc()
    {
            $inst_code = Request::param('inst_code');
            $sql = "select * from employee where  inst_code =? order by employee_code"; 
            $result= Db::query($sql, [$inst_code]);
            if ($result == null || count($result) === 0) {
                $data['message'] = 'success';
                return json(['data' => [], 'code' => 200]);
            } else {
                $data['message'] = 'success';
                return json(['data' => $result, 'code' => 200]);
            }
                
    }

    //导入库存
    public function importKc()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');

        // 开始事务
        Db::startTrans();

        try {
            foreach ($data as $item) {
                // 提取每条数据的字段
                $material_code = $item['物料编码'];
                $material_type = $item['物料类型'];
                $material_name = $item['物料名称'];
                $material_unit = $item['物料单位'];
                $material_specification = $item['物料规格'];
                $procurement_time = $item['采购年份'];
                $consumable = $item['是否易耗品'];
                $inventory_quantity = $item['库存数量'];
                $available_quantity = $item['可用数量'];
                $material_price = $item['物料价格'];
                $cost_type = $item['费用类型'];
                $procurement_method = $item['采购方式'];
                $project_name = $item['采购项目名称'];
                $supplier_name = $item['供应商名称'];
                $history_type = '0';
                if( $inventory_quantity  === 0){
                    $history_type='1';
                }
                // 日期转换处理
                $epoch = strtotime('1900-01-01');
                $warranty_period = date('Y-m-d', $epoch + ($item['质保到期时间'] - 1) * 86400);
                $delay_time = date('Y-m-d', $epoch + ($item['延期时间'] - 1) * 86400);
                $release_time = date('Y-m-d', $epoch + ($item['物料发放时间'] - 1) * 86400);
                $end_time = date('Y-m-d', $epoch + ($item['物料发放结束时间'] - 1) * 86400);
                $creation_time = date('Y-m-d');
                $creater_code = $userId;

                // 查询是否已有数据
                $searchList = Db::table('materialinfo')
                    ->where('inst_code', '100001')
                    ->where('material_code', $material_code)
                    ->find();

                if ($searchList === null || count($searchList) === 0) {
                    // 插入 materialinfo 表
                    try {
                        Db::table('materialinfo')->insert([
                            'material_code' => $material_code,
                            'material_type' => $material_type,
                            'material_name' => $material_name,
                            'material_unit' => $material_unit,
                            'material_specification' => $material_specification,
                            'procurement_time' =>  $procurement_time,
                            'consumable' => $consumable,
                            'inst_code' => '100001',
                            'inventory_quantity' => $inventory_quantity,
                            'available_quantity' => $available_quantity,
                            'material_price' => $material_price,
                            'cost_type' => $cost_type,
                            'procurement_method' => $procurement_method,
                            'project_name' => $project_name,
                            'supplier_name' => $supplier_name,
                            'creation_time'=> $creation_time,
                            'creater_code'=> $creater_code,
                            'warranty_period'=> $warranty_period,
                            'delay_time'=> $delay_time,
                            'release_time'=> $release_time,
                            'end_time'=> $end_time,
                            'history_type'=> $history_type,
                        ]); 
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['data' => ['message' => '数据库插入失败'], 'code' => 300]);
                    }
                    try {
                        Db::table('materialinfo_record')->insert([
                            'material_code' => $material_code,
                            'material_name' => $material_name,
                            'quantity' => 0,
                            'inventory_quantity' => $inventory_quantity,
                            'inst_code' => '100001',
                            'type' => '库存导入',
                            'allocate_time' => date('Y-m-d H:i:s'), // 使用当前时间
                            'allocate_person' => $userId, // 使用当前时间
                        ]);
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['data' => ['message' => '数据库分配记录插入失败'], 'code' => 300]);
                    }
                } else {
                    Db::rollback();
                    return json(['data' => ['message' => "已有物料数据，编号为: " . $searchList['material_code']], 'code' => 300]);
                    
                }
            }
            // 提交事务
            Db::commit();
            return json(['data' => ['message' => '库存导入成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    //库存分配
    public function alloateKc()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');
        $allocateCode = Request::param('allocateCode');
        Db::startTrans();

        try {
            foreach ($data as $item) {
                // 提取每条数据的id和name用于判断是否存在
                $material_code = $item['物料编码'];
                $material_name = $item['物料名称'];
                $inst_code = $item['分配市场部编码'];
                $inst_name = $item['分配市场部名称'];
                $inventory_quantity = $item['分配数量'];
                $history_type='0';
                // 日期转换处理
                $allocate_time =  date('Y-m-d H:i:s');
                $allocate_person = $userId;
                $searchList = Db::table('materialinfo')->where('inst_code',$allocateCode)->where('history_type', '0')->where('material_code',$material_code)->find();
                $searchList1 = Db::table('inst')->where('inst_code',$inst_code)->find();
                $searchList2 = Db::table('materialinfo')->where('inst_code',$inst_code)->where('material_code',$material_code)->find();
                if ($searchList == null || count($searchList) === 0) {
                    Db::rollback();
                    return json(['data' => ['message' => "市场部编码为: " .$allocateCode. "不存在物料编码为: " .$material_code. "的信息，无法分配"], 'code' => 300]);
                } else {
                    if ($searchList1 == null || count($searchList1) === 0) {
                        Db::rollback();
                        return json(['data' => ['message' => "无分配市场部编码，编码为: " .$inst_code], 'code' => 300]);
                    } else {
                        if ((float)$searchList['inventory_quantity'] < (float)$inventory_quantity){
                            Db::rollback();
                            return json(['data' => ['message' => "分配数量大于库存数量，物料编码为: " .$material_code], 'code' => 300]);
                        } else {
                            if($searchList2 == null || count($searchList2) === 0){
                                try {
                                    Db::table('materialinfo')->insert([
                                        'material_code' => $searchList['material_code'],
                                        'material_type' => $searchList['material_type'],
                                        'material_name' => $searchList['material_name'],
                                        'material_unit' => $searchList['material_unit'],
                                        'material_specification' => $searchList['material_specification'],
                                        'procurement_time' =>  $searchList['procurement_time'],
                                        'consumable' => $searchList['consumable'],
                                        'inst_code' => $inst_code,
                                        'inventory_quantity' => $inventory_quantity ,
                                        'available_quantity' => $inventory_quantity ,
                                        'material_price' => $searchList['material_price'],
                                        'cost_type' => $searchList['cost_type'],
                                        'procurement_method' => $searchList['procurement_method'],
                                        'project_name' => $searchList['project_name'],
                                        'supplier_name' => $searchList['supplier_name'],
                                        'creation_time'=> $allocate_time,
                                        'creater_code'=> $allocate_person,
                                        'warranty_period'=> $searchList['warranty_period'],
                                        'delay_time'=> $searchList['delay_time'],
                                        'release_time'=> $searchList['release_time'],
                                        'end_time'=> $searchList['end_time'],
                                        'history_type'=> $inventory_quantity===0?'1':'0'
                                    ]); 
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库插入失败'], 'code' => 300]);
                                }
                                try {
                                    Db::table('materialinfo_record')->insert([
                                        'material_code' => $material_code,
                                        'material_name' => $material_name,
                                        'quantity' =>0,
                                        'allocate_quantity' => $searchList['inventory_quantity'],
                                        'allocate_code' =>$allocateCode,
                                        'inventory_quantity' => $inventory_quantity,
                                        'inst_code' => $inst_code,
                                        'type' => '库存分配',
                                        'allocate_time' => date('Y-m-d H:i:s'), // 使用当前时间
                                        'allocate_person' => $userId, // 使用当前时间
                                    ]);
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
                                }
                            }else{
                                try {
                                    $searchInventoryQuantity = (float)$searchList2['inventory_quantity'];
                                    $currentInventoryQuantity = (float)$inventory_quantity;
                                    Db::table('materialinfo')
                                        ->where('inst_code',$inst_code)->where('material_code',$material_code)
                                        ->update([
                                            'inventory_quantity' => $searchInventoryQuantity + $currentInventoryQuantity,
                                            'available_quantity' => $searchInventoryQuantity + $currentInventoryQuantity,
                                            'history_type' => $searchInventoryQuantity + $currentInventoryQuantity > 0?'0':'1',
                                        ]);
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库更新失败'], 'code' => 300]);
                                }
                                try {
                                    Db::table('materialinfo_record')->insert([
                                        'material_code' => $material_code,
                                        'material_name' => $material_name,
                                        'quantity' => $searchList2['inventory_quantity'],
                                        'inventory_quantity' => $inventory_quantity,
                                        'inst_code' => $inst_code,
                                        'allocate_quantity' => $searchList['inventory_quantity'],
                                        'allocate_code' =>$allocateCode,
                                        'type' => '库存分配',
                                        'allocate_time' => date('Y-m-d H:i:s'), // 使用当前时间
                                        'allocate_person' => $userId, // 使用当前时间
                                    ]);
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
                                }
                            }
                            try {
                                $searchInventoryQuantity = (float)$searchList['inventory_quantity'];
                                $currentInventoryQuantity = (float)$inventory_quantity;
                                if($searchInventoryQuantity - $currentInventoryQuantity==0){
                                    $history_type='1';
                                }
                                Db::table('materialinfo')
                                    ->where('inst_code',$allocateCode)->where('material_code',$material_code)
                                    ->update([
                                        'inventory_quantity' => $searchInventoryQuantity - $currentInventoryQuantity,
                                        'available_quantity' => $searchInventoryQuantity - $currentInventoryQuantity,
                                        'history_type' => $history_type
                                    ]);
                            } catch (Exception $e) {
                                Db::rollback();
                                return json(['data' => ['message' => '数据库更新失败'], 'code' => 300]);
                            }
                        }
                    }
                }
            } 
            Db::commit();
            return json(['data' => ['message' => '库存分配成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    //物资种类表
    public function wzType()
    {
        $searchList = Db::table('materialinfo')
        ->where('inst_code', '100001')
        ->where('history_type', '0')
        ->select();
        //更新
        if ($searchList == null || count($searchList) === 0) {
            return json(['data' => [], 'code' =>200]);
        } else {
            return json(['data' => $searchList , 'code' =>200]);
        }
    }
    
    
    //库存移送到历史库存
    public function givehistoryKc()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');
        Db::startTrans();
        try {
            foreach ($data as $item) {
                // 提取每条数据的id和name用于判断是否存在
                $material_code = $item['物料编码'];
                $material_name = $item['物料名称'];
                $history_type='1';
                // 日期转换处理
                $allocate_time =  date('Y-m-d H:i:s');
                $allocate_person = $userId;
                $searchList = Db::table('materialinfo')->where('history_type', '0')->where('material_code',$material_code)->find();
                if ($searchList == null || count($searchList) === 0) {
                    Db::rollback();
                    return json(['data' => ['message' =>  "不存在物料编码为: " .$material_code. "的现有库存信息，无法分配移送"], 'code' => 300]);
                } else {
                    try {
                        Db::table('materialinfo')
                            ->where('material_code',$material_code)
                            ->update([
                                'history_type' =>'1'
                            ]);
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['data' => ['message' => '数据库更新失败'], 'code' => 300]);
                    }
                    try {
                        Db::table('materialinfo_record')->insert([
                            'material_code' => $material_code,
                            'material_name' => $material_name,
                            'type' => '现有库存移送历史库存',
                            'allocate_time' => date('Y-m-d H:i:s'), // 使用当前时间
                            'allocate_person' => $userId, // 使用当前时间
                        ]);
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
                    }
                }
            } 
            Db::commit();
            return json(['data' => ['message' => '现有库存移送历史成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    
    //查询历史库存记录
    public function searchHistoryCk()
    {
        $instCode = Request::param('instCode');
        $materialName = Request::param('materialName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList = Db::table('materialinfo')->where('inst_code', $instCode)
        ->where('history_type', '1')
        ->where('material_name', 'like', '%' . $materialName . '%')
        ->page($page, $pageSize)->select();
        $total =  Db::table('materialinfo')->where('inst_code', $instCode)
        ->where('history_type', '1')
        ->where('material_name', 'like', '%' . $materialName . '%')
        ->count();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'total' => 0, 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    //导出历史库存记录
    public function exportHistoryCk()
    {
        $instCode = Request::param('instCode');
        $materialName = Request::param('materialName');
        $searchList = Db::table('materialinfo')->where('inst_code', $instCode)
        ->where('history_type', '1')->where('material_name', 'like', '%' . $materialName . '%')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //查询分配库存记录
    public function searchAllocateCk()  
    {
        $instCode = Request::param('instCode'); 
        $materialName = Request::param('materialName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10); 
        $searchList = Db::table('materialinfo_record');
        $searchList1 = Db::table('materialinfo_record');
        if (!empty($instCode)) { 
            $searchList = $searchList->where('materialinfo_record.inst_code', $instCode);
            $searchList1 = $searchList1->where('materialinfo_record.inst_code', $instCode); 
        }
        if (!empty($materialName)) {
            $searchList = $searchList->where('material_name', 'like', '%' . $materialName . '%'); 
            $searchList1 = $searchList1->where('material_name', 'like', '%' . $materialName . '%');
        }
        $searchList = $searchList
        ->leftJoin('inst', 'materialinfo_record.inst_code = inst.inst_code')
        ->where('type', '库存分配')
        ->field(['materialinfo_record.*', 'inst.inst_name'])
        ->page($page, $pageSize); 
        // 分页
        $result = $searchList->select()->toArray(); 
        // 查询数据
        $total =  $searchList1
        ->where('type', '库存分配')->count(); 
        // 总记录数
        if (empty($result)) {
            return json(['data' => [], 'code' => 200,'total' => 0, 'message' => 'success']);
        } else { 
            return json(['data' => $result, 'code' => 200,'total' => $total, 'message' => 'success']);
        }     
    }
    //导出分配库存记录
    public function exportAllocateCk()
    {
        $instCode = Request::param('instCode');
        $materialName = Request::param('materialName');
        $searchList = Db::table('materialinfo_record');
        if (!empty($instCode)) {
            $searchList = $searchList->where('materialinfo_record.inst_code', $instCode);
        }
        if (!empty($materialName)) {
            $searchList = $searchList->where('material_name', 'like', '%' . $materialName . '%');
        }

        $searchList = $searchList->leftJoin('inst', 'materialinfo_record.inst_code = inst.inst_code')
                                ->where('type', '库存分配')
                                ->select(['materialinfo_record.*', 'inst.inst_name']); // 查询字段
        $result = $searchList->toArray(); // 执行查询并转为数组
        if (empty($result)) {
            return json(['data' => [], 'code' => 200, 'message' => 'success']);
        } else {
            return json(['data' => $result, 'code' => 200, 'message' => 'success']);
        }
    }

    public function generateBusiId() {
        list($usec, $sec) = explode(" ", microtime());
        $date = date('YmdHis');
        $microseconds = substr($usec, 2, 6);
        return $date. $microseconds;
    }
    //提交需求预估流程
    public function submitDemand()
    {
        $loanData = Request::param('loanData');
        $user_id = Request::param('user_id');
        $user_name = Request::param('user_name');
        $inst_code = Request::param('inst_code');
        $inst_name = Request::param('inst_name');
        $telephone = Request::param('telephone');
        $flow_no = Request::param('flow_no');
        $flow_title = Request::param('flow_title');
        $flow_node = Request::param('flow_node');
        $flow_node_name = Request::param('flow_node_name');
        $approval_name = Request::param('approval_name');
        $approval_content = Request::param('approval_content');
        $next_approval_id = Request::param('next_approval_id');
        $next_approval_name = Request::param('next_approval_name');
        $busi_id=Request::param('busi_id');
        $time =  date('Y-m-d H:i:s');
        Db::startTrans();
        try {
            //第一步没有busi_id插入，有则第一步退回后更新
            if(empty($busi_id)){
                $busi_id = $this->generateBusiId();
                foreach ($loanData as $item) {
                    $material_code = $item['material_code'];
                    $material_name = $item['material_name'];
                    $material_type = $item['material_type'];
                    $consumable = $item['consumable'];
                    $num = $item['num'];
                    try {
                        Db::table('demand_forecast')->insert([
                            'busi_id' =>$busi_id,
                            'material_code' => $material_code,
                            'material_name' => $material_name,
                            'material_type' => $material_type,
                            'consumable' => $consumable,
                            'num' =>  $num,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'inst_code' => $inst_code ,
                            'inst_name' => $inst_name ,
                            'telephone' => $telephone,
                            'time' =>  $time,
                        ]); 
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['message' => '数据库插入失败', 'code' => 300]);
                    }
                } 
            }else{
                //退回后再提交删除原来数据，新增新的数据
                if($flow_node==='1'){
                    DB::table('demand_forecast')->where("busi_id",$busi_id)->delete();
                    foreach ($loanData as $item) {
                        $material_code = $item['material_code'];
                        $material_name = $item['material_name'];
                        $material_type = $item['material_type'];
                        $consumable = $item['consumable'];
                        $num = $item['num'];
                        try {
                            Db::table('demand_forecast')->insert([
                                'busi_id' =>$busi_id,
                                'material_code' => $material_code,
                                'material_name' => $material_name,
                                'material_type' => $material_type,
                                'consumable' => $consumable,
                                'num' =>  $num,
                                'flow_no' => $flow_no,
                                'flow_title' => $flow_title,
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'inst_code' => $inst_code ,
                                'inst_name' => $inst_name ,
                                'telephone' => $telephone,
                                'time' =>  $time,
                            ]); 
                        } catch (Exception $e) {
                            Db::rollback();
                            return json(['message' => '数据库插入失败', 'code' => 300]);
                        }
                    } 
                }
            }
            try { 
                //发起插入当前和后一节点，之后审批更新当前节点，后一节点不为-99时插入后一节点
                $nextFlowNode= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->find();
                if($flow_node==='1'){
                    $currentFlowApproval= Db::table('flow_approval') ->where('busi_id', $busi_id)->where('flow_node',  $flow_node)->find();
                    if ($currentFlowApproval == null || count($currentFlowApproval) === 0) {
                        Db::table('flow_approval')->insert([
                            'busi_id' =>$busi_id,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'flow_node' => $flow_node,
                            'flow_node_name' => $flow_node_name,
                            'approval_id' => $user_id,
                            'approval_name' => $approval_name,
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time,
                            'show_type' => '1'
                        ]); 
                    } else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_time' => $time
                        ]); 
                    }
                }else{
                    //如果当前节点不是最后一个节点
                    if($nextFlowNode['flow_node_next'] != '-99'){
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '5',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)
                        ->update([
                            'flow_status' => '5'
                        ]); 
                    }
                }       
                if($nextFlowNode['flow_node_next'] != '-99'){
                    $flow_node_next = $nextFlowNode['flow_node_next'];
                    $nextList= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->find();
                    $flow_node_name_next = $nextList['flow_node_name'];
                    $nextResultList= Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->select();
                    if($nextResultList  === null || count($nextResultList) === 0){
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)
                        ->update([
                            'show_type' => '0'
                        ]); 
                    }
                    Db::table('flow_approval')->insert([
                        'busi_id' =>$busi_id,
                        'flow_no' => $flow_no,
                        'flow_title' => $flow_title,
                        'flow_node' => $flow_node_next,
                        'flow_node_name' => $flow_node_name_next,
                        'approval_id' => $next_approval_id,
                        'approval_name' => $next_approval_name,
                        'approve_status' => '1',
                        'flow_status' => '1',
                        'approval_content' => '',
                        'show_type' => '1'
                    ]); 
                }
            } catch (Exception $e) {
                Db::rollback();
                return json(['message' => '数据库插入失败', 'code' => 300]);
            }
            Db::commit();
            if($flow_node==='1'){
                return json(['message' => '流程已提交', 'code' => 200]);
            }else{
                return json(['message' => '流程已审批', 'code' => 200]);
            }
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['message' => '操作失败', 'code' => 300]);
        }

        
    }
    
    //提交需求预估汇总流程
    public function submitDemandTotal()
    {
        $loanData = Request::param('loanData');
        $user_id = Request::param('user_id');
        $year = Request::param('year');
        $user_name = Request::param('user_name');
        $inst_code = Request::param('inst_code');
        $inst_name = Request::param('inst_name');
        $dateRange = Request::param('dateRange');
        $time_start =  date('Y-m-d', strtotime($dateRange[0]));
        $time_end =date('Y-m-d', strtotime($dateRange[1]));
        $flow_no = Request::param('flow_no');
        $flow_title = Request::param('flow_title');
        $flow_node = Request::param('flow_node');
        $flow_node_name = Request::param('flow_node_name');
        $approval_name = Request::param('approval_name');
        $approval_content = Request::param('approval_content');
        $next_approval_id = Request::param('next_approval_id');
        $next_approval_name = Request::param('next_approval_name');
        $busi_id=Request::param('busi_id');
        $time =  date('Y-m-d H:i:s');
        Db::startTrans();
        try {
            //第一步没有busi_id插入，有则第一步退回后更新
            if(empty($busi_id)){
                $busi_id = $this->generateBusiId();
                foreach ($loanData as $item) {
                    $material_code = $item['material_code'];
                    $material_name = $item['material_name'];
                    $material_type = $item['material_type'];
                    $consumable = $item['consumable'];
                    $num = $item['num'];
                    try {
                        Db::table('demand_forecast_total')->insert([
                            'busi_id' =>$busi_id,
                            'material_code' => $material_code,
                            'material_name' => $material_name,
                            'material_type' => $material_type,
                            'year' => $year,
                            'consumable' => $consumable,
                            'num' =>  $num,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'inst_code' => $inst_code ,
                            'inst_name' => $inst_name ,
                            'time_start' => $time_start,
                            'time_end' => $time_end,
                            'time' =>  $time,
                        ]); 
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['message' => '数据库插入失败', 'code' => 300]);
                    }
                } 
            }else{
                //退回后再提交删除原来数据，新增新的数据
                if($flow_node==='1'){
                    DB::table('demand_forecast_total')->where("busi_id",$busi_id)->delete();
                    foreach ($loanData as $item) {
                        $material_code = $item['material_code'];
                        $material_name = $item['material_name'];
                        $material_type = $item['material_type'];
                        $consumable = $item['consumable'];
                        $num = $item['num'];
                        try {
                            Db::table('demand_forecast_total')->insert([
                                'busi_id' =>$busi_id,
                                'material_code' => $material_code,
                                'material_name' => $material_name,
                                'material_type' => $material_type,
                                'year' => $year,
                                'consumable' => $consumable,
                                'num' =>  $num,
                                'flow_no' => $flow_no,
                                'flow_title' => $flow_title,
                                'user_id' => $user_id,
                                'user_name' => $user_name,
                                'inst_code' => $inst_code ,
                                'inst_name' => $inst_name ,
                                'time_start' => $time_start,
                                'time_end' => $time_end,
                                'time' =>  $time,
                            ]); 
                        } catch (Exception $e) {
                            Db::rollback();
                            return json(['message' => '数据库插入失败', 'code' => 300]);
                        }
                    } 
                }
            }
            try { 
                //发起插入当前和后一节点，之后审批更新当前节点，后一节点不为-99时插入后一节点
                $nextFlowNode= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->find();
                if($flow_node==='1'){
                    $currentFlowApproval= Db::table('flow_approval') ->where('busi_id', $busi_id)->where('flow_node',  $flow_node)->find();
                    if ($currentFlowApproval == null || count($currentFlowApproval) === 0) {
                        Db::table('flow_approval')->insert([
                            'busi_id' =>$busi_id,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'flow_node' => $flow_node,
                            'flow_node_name' => $flow_node_name,
                            'approval_id' => $user_id,
                            'approval_name' => $approval_name,
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time,
                            'show_type' => '1'
                        ]); 
                    } else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_time' => $time
                        ]); 
                    }
                }else{
                    //如果当前节点不是最后一个节点
                    if($nextFlowNode['flow_node_next'] != '-99'){
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '5',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)
                        ->update([
                            'flow_status' => '5'
                        ]); 
                    }
                }       
                if($nextFlowNode['flow_node_next'] != '-99'){
                    $flow_node_next = $nextFlowNode['flow_node_next'];
                    $nextList= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->find();
                    $flow_node_name_next = $nextList['flow_node_name'];
                    $nextResultList= Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->select();
                    if($nextResultList  === null || count($nextResultList) === 0){
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)
                        ->update([
                            'show_type' => '0'
                        ]); 
                    }
                    Db::table('flow_approval')->insert([
                        'busi_id' =>$busi_id,
                        'flow_no' => $flow_no,
                        'flow_title' => $flow_title,
                        'flow_node' => $flow_node_next,
                        'flow_node_name' => $flow_node_name_next,
                        'approval_id' => $next_approval_id,
                        'approval_name' => $next_approval_name,
                        'approve_status' => '1',
                        'flow_status' => '1',
                        'approval_content' => '',
                        'show_type' => '1'
                    ]); 
                }
            } catch (Exception $e) {
                Db::rollback();
                return json(['message' => '数据库插入失败', 'code' => 300]);
            }
            Db::commit();
            if($flow_node==='1'){
                return json(['message' => '流程已提交', 'code' => 200]);
            }else{
                return json(['message' => '流程已审批', 'code' => 200]);
            }
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['message' => '操作失败', 'code' => 300]);
        }

        
    }
    //查询下一审批人
    public function searchNextApproval()
    {
        $inst_code = Request::param('inst_code');
        $flow_no = Request::param('flow_no');
        $flow_node = Request::param('flow_node');
        if($flow_no==='1' || $flow_no==='2' || $flow_no==='3' || $flow_no==='4'){
            //客户经理发起本机构审批，其他节点审批人为上层机构审批
            if($flow_node==='1'){
                $instList = Db::table('inst')->where('inst_code', $inst_code)->find();
                $up_inst_code =$instList['inst_code'];
                $up_inst_name =$instList['inst_name'];
            }else{
                $instList = Db::table('inst')->where('inst_code', $inst_code)->find();
                $up_inst_code =$instList['up_inst_code'];
                $up_inst_name =$instList['up_inst_name'];
            }
            try {
                $roleIdList = Db::table('flow')->where('flow_no', $flow_no)->where('flow_node_pre', $flow_node)->find();
                $role_id=$roleIdList['role_id'];
                $result = Db::table('employee')
                    ->where('inst_code', $up_inst_code)
                    ->join('user_role', 'employee.employee_code = user_role.user_id', 'inner')
                    ->where('user_role.role_id', '=', $role_id)
                    ->select(); 
                return json(['data' => $result, 'code' => 200]);
            }  catch (Exception $e) {
                return json(['message' => '查询审批人失败', 'code' => 300]);
            }
        }else if($flow_no==='5' ){
            $inst_code='100001';
            try {
                $roleIdList = Db::table('flow')->where('flow_no', $flow_no)->where('flow_node_pre', $flow_node)->find();
                $role_id=$roleIdList['role_id'];
                $result = Db::table('employee')
                    ->where('inst_code', $inst_code)
                    ->join('user_role', 'employee.employee_code = user_role.user_id', 'inner')
                    ->where('user_role.role_id', '=', $role_id)
                    ->select(); 
                return json(['data' => $result, 'code' => 200]);
            }  catch (Exception $e) {
                return json(['message' => '查询审批人失败', 'code' => 300]);
            }
        }
    }
    //查询待办
    public function searchTodo()
    {
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $user_id=Request::param('user_id');
        $busi_id=Request::param('busi_id');
        $approve_status=Request::param('approve_status');
        try {
            $resultList = Db::table('flow_approval')->where('show_type','1')->page($page, $pageSize)
            ->order('approval_time', 'DESC');
            $total = Db::table('flow_approval')->where('show_type','1');
            if (!empty($approve_status)) {
                $resultList = $resultList->where('approve_status','1');;
                $total = $total->where('approve_status','1');;
            }
            if (!empty($busi_id)) {
                $resultList = $resultList->where('busi_id',$busi_id);
                $total = $total->where('busi_id',$busi_id);
            }
            if (!empty($user_id)) {
                $resultList = $resultList->where('approval_id', $user_id);
                $total = $total->where('approval_id', $user_id);
            }
            $resultList = $resultList->select();
            $total =  $total->count();
            return json(['data' => $resultList, 'total'=>$total,'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询待办失败', 'total'=>'0','code' => 300]);
        }
    }
    //查询已办
    public function searchDone()
    {
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $user_id=Request::param('user_id');
        $busi_id=Request::param('busi_id');
        $flow_title=Request::param('flow_title');
        $apply_name=Request::param('apply_name');
        try {
            $resultList = Db::table('flow_approval')
            ->where('approval_id', $user_id)
            ->where('approve_status','0')
            ->order('approval_time', 'DESC');
            if (!empty($busi_id)) {
                $resultList = $resultList->where('busi_id',$busi_id);
            }
            if (!empty($flow_title)) {
                $resultList = $resultList->where('flow_title', 'like', '%' . $flow_title . '%');
            }
           $resultList = $resultList->select();
            // 用于存储最终处理后的数据
            $finalResultList = [];
            // 用于临时存储已经处理过的busi_id
            $processedBusiIds = [];
            foreach ($resultList as $row) {
                $busi_id = $row['busi_id'];
                if ($row['flow_no'] == '1' || $row['flow_no'] == '2') {
                    $resultList1 = Db::table('demand_forecast')
                    ->where('busi_id', $busi_id)
                    ->find();
                    $row['apply_id'] = $resultList1['user_id'];
                    $row['apply_name'] = $resultList1['user_name'];
                    $row['time'] =$resultList1['time'];
                    $row['url'] ='/materialIssuance/demandForecastApprove';
                }else if ($row['flow_no'] == '3' || $row['flow_no'] == '4') {
                    $resultList1 = Db::table('wz_apply')
                    ->where('busi_id', $busi_id)
                    ->find();
                    $row['apply_id'] = $resultList1['user_id'];
                    $row['apply_name'] = $resultList1['user_name'];
                    $row['time'] =$resultList1['time'];
                    $row['url'] ='/materialIssuance/reviewApprove';
                }else if ($row['flow_no'] == '5') {
                    $resultList1 = Db::table('demand_forecast_total')
                    ->where('busi_id', $busi_id)
                    ->find();
                    $row['apply_id'] = $resultList1['user_id'];
                    $row['apply_name'] = $resultList1['user_name'];
                    $row['time'] =$resultList1['time'];
                    $row['url'] ='/materialIssuance/demandForecastTotalApplyApprove';
                }
                if (strpos($row['apply_name'], $apply_name) === false) {
                    continue;
                }
                if (!in_array($busi_id, $processedBusiIds)) {
                    // 如果当前busi_id第一次出现，直接添加到最终结果列表
                    $finalResultList[] = $row;
                    $processedBusiIds[] = $busi_id;
                } else {
                    // 如果当前busi_id已经出现过，检查show_type是否为1
                    if ($row['show_type'] == 1) {
                        // 如果show_type为1，替换掉之前添加的该busi_id对应的记录
                        foreach ($finalResultList as $key => $finalRow) {
                            if ($finalRow['busi_id'] == $busi_id) {
                                $finalResultList[$key] = $row;
                                break;
                            }
                        }
                    }
                }
            }
            $total = count($finalResultList);
            $page = max(1, intval($page)); // 确保页码最小为1
            $pageSize = max(1, intval($pageSize)); // 确保每页数量最小为1
            $startIndex = ($page - 1) * $pageSize;
            $endIndex = min($startIndex + $pageSize, $total);
            $resultList1 = array_slice($finalResultList, $startIndex, $pageSize);
            return json(['data' => $resultList1, 'total'=>$total,'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询已办失败', 'total'=>'0','code' => 300]);
        }
    }
    //查询需求预估
    public function searchDemand()
    {
        $busi_id=Request::param('busi_id');
        try {
            $resultList = Db::table('demand_forecast')
            ->where('busi_id', $busi_id)
            ->select();
            return json(['data' => $resultList, 'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询需求预估失败', 'code' => 300]);
        }
    }
    //查询需求预估汇总申请
    public function searchDemandApplyTotal()
    {
        $busi_id=Request::param('busi_id');
        try {
            $resultList = Db::table('demand_forecast_total')
            ->where('busi_id', $busi_id)
            ->select();
            return json(['data' => $resultList, 'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询需求预估汇总申请失败', 'code' => 300]);
        }
    }
    //查询当前是不是最后一个节点
    public function searchMaxFlowNode()
    {
        $flow_no=Request::param('flow_no');
        try {
            $resultList = Db::table('flow')
            ->where('flow_no', $flow_no)
            ->max('flow_node');
            return json(['data' => $resultList, 'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询最后节点失败', 'code' => 300]);
        }
    }
    
    //取消流程
    public function cancel()
    {
        $busi_id=Request::param('busi_id');
        try {
            Db::table('flow_approval')->where('busi_id', $busi_id)
            ->update([
                'approve_status' => '0',
                'flow_status' => '4'
            ]); 
            return json(['message' => '取消流程成功', 'code' => 200]);
        }  catch (Exception $e) {
            return json(['message' => '取消流程失败', 'code' => 300]);
        }
    }
    //否决流程
    public function deny()
    {
        $busi_id=Request::param('busi_id');
        $flow_node=Request::param('flow_node');
        $time =  date('Y-m-d H:i:s');
        $approval_content=Request::param('approval_content');
        Db::startTrans();
        try {
            Db::table('flow_approval')->where('busi_id', $busi_id)
            ->update([
                'approve_status' => '0',
                'flow_status' => '3'
            ]); 
            Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
            ->update([
                'approval_content' => $approval_content,
                'approval_time' => $time
            ]); 
            Db::commit();
            return json(['message' => '否决流程成功', 'code' => 200]);
        }  catch (Exception $e) {
            Db::rollback();
            return json(['message' => '否决流程失败', 'code' => 300]);
        }
    }
    //退回第一步流程
    public function backfirst()
    {
        $busi_id=Request::param('busi_id');
        $flow_node=Request::param('flow_node');
        $time =  date('Y-m-d H:i:s');
        $approval_content=Request::param('approval_content');
        Db::startTrans();
        try {
            
            Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
            ->update([
                'approval_content' => $approval_content,
                'approval_time' => $time
            ]); 
            Db::table('flow_approval')->where('busi_id', $busi_id)
            ->update([
                'approve_status' => '0',
                'flow_status' => '2',
                'show_type' => '0'
            ]); 
            $resultFirst=Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', '1')->find();
            Db::table('flow_approval')->insert([
                'busi_id' =>$busi_id,
                'flow_no' => $resultFirst['flow_no'],
                'flow_title' => $resultFirst['flow_title'],
                'flow_node' => $resultFirst['flow_node'],
                'flow_node_name' => $resultFirst['flow_node_name'],
                'approval_id' => $resultFirst['approval_id'],
                'approval_name' => $resultFirst['approval_name'],
                'approve_status' => '1',
                'flow_status' => '1',
                'approval_content' => '',
                'show_type' => '1'
            ]); 
            Db::commit();
            return json(['message' => '退回第一步成功', 'code' => 200]);
        }  catch (Exception $e) {
            Db::rollback();
            return json(['message' => '退回第一步失败', 'code' => 300]);
        }
    }
    //退回上一步流程
    public function backlast()
    {
        $busi_id=Request::param('busi_id');
        $flow_node=Request::param('flow_node');
        $time =  date('Y-m-d H:i:s');
        $approval_content=Request::param('approval_content');
        Db::startTrans();
        try {
            Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
            ->update([
                'approve_status' => '0',
                'flow_status' => '2',
                'show_type' => '0',
                'approval_content' => $approval_content,
                'approval_time' => $time
            ]); 
            $flowNoList=Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->find();
            $flow_no=$flowNoList['flow_no'];
            $flow_node_preList=Db::table('flow')->where('flow_no', $flow_no)->where('flow_node', $flow_node)->find();
            $flow_node_pre=$flow_node_preList['flow_node_pre'];
            Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node_pre)
            ->update([
                'approve_status' => '0',
                'flow_status' => '2',
                'show_type' => '0'
            ]); 
            $resultFirst=Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node',$flow_node_pre)->find();
            Db::table('flow_approval')->insert([
                'busi_id' =>$busi_id,
                'flow_no' => $resultFirst['flow_no'],
                'flow_title' => $resultFirst['flow_title'],
                'flow_node' => $resultFirst['flow_node'],
                'flow_node_name' => $resultFirst['flow_node_name'],
                'approval_id' => $resultFirst['approval_id'],
                'approval_name' => $resultFirst['approval_name'],
                'approve_status' => '1',
                'flow_status' => '1',
                'approval_content' => '',
                'show_type' => '1'
            ]); 
            Db::commit();
            return json(['message' => '退回上一步成功', 'code' => 200]);
        }  catch (Exception $e) {
            Db::rollback();
            return json(['message' => '退回上一步失败', 'code' => 300]);
        }
    }
    //流程跟踪
    public function trace()
    {
        $busi_id=Request::param('busi_id');
        $result=Db::table('flow_approval')->where('busi_id', $busi_id)->order('id')->select();
        return json(['data' => $result, 'code' => 200]);
           
    }
    
    //查询需求预估明细
    public function searchDemandMx()
{
    $page = Request::param('page', 1);
    $pageSize = Request::param('pageSize', 10);
    $inst_code = Request::param('instCode');
    $user_name = Request::param('user_name');
    $time = Request::param('time');
    $timeStart =  date('Y-m-d', strtotime($time[0]));
    $timeEnd =date('Y-m-d', strtotime($time[1]));

    if ($inst_code === '100001') {
        $inst_code_array = Db::table('inst')->select()->column('inst_code');
    } else if (strlen($inst_code) === 6) {
        $inst_code_array = Db::table('inst')->where('up_inst_code', $inst_code)->column('inst_code');
    } else {
        $inst_code_array = [$inst_code];
    }
    $inst_code_str = "'". implode("', '", $inst_code_array)."'";
    $offset = ($page - 1) * $pageSize;
    $sql = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
        SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
        SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
        ) a INNER JOIN (
        SELECT * FROM demand_forecast where DATE(time)>? and DATE(time)<? and inst_code in (".$inst_code_str.")  and user_name LIKE CONCAT('%',?, '%')
        ) b ON a.busi_id = b.busi_id 
        INNER JOIN materialinfo c ON b.material_code = c.material_code 
        ) AS subquery GROUP BY material_code,material_name,material_type,consumable
         LIMIT {$offset}, {$pageSize};";
    $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
        SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
        SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
        ) a INNER JOIN (
        SELECT * FROM demand_forecast where DATE(time)>? and DATE(time)<? and inst_code in (".$inst_code_str.") and user_name LIKE CONCAT('%',?, '%')
        ) b ON a.busi_id = b.busi_id 
        INNER JOIN materialinfo c ON b.material_code = c.material_code 
        ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
    $result = Db::query($sql, [$timeStart, $timeEnd,$user_name]);
    $result1= Db::query($sql1, [$timeStart, $timeEnd,$user_name]);
    return json(['data' => $result, 'total' => count($result1),'code' => 200]);
 }
  //查询需求预估汇总明细
  public function searchDemandMxTotal()
  {
      $page = Request::param('page', 1);
      $pageSize = Request::param('pageSize', 10);
      $inst_code = Request::param('instCode');
      $year = Request::param('year');
      if(empty($inst_code)){
        $offset = ($page - 1) * $pageSize;
        $sql = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=?  
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable
            LIMIT {$offset}, {$pageSize};";
        $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=? 
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
        $result = Db::query($sql, [$year]);
        $result1= Db::query($sql1, [$year]);
        return json(['data' => $result, 'total' => count($result1),'code' => 200]);
      }else{
        $offset = ($page - 1) * $pageSize;
        $sql = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=?  and inst_code=?
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable
            LIMIT {$offset}, {$pageSize};";
        $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=?  and inst_code=?
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
        $result = Db::query($sql, [$year,$inst_code]);
        $result1= Db::query($sql1, [$year,$inst_code]);
        return json(['data' => $result, 'total' => count($result1),'code' => 200]);
      }
   }
  //导出需求预估明细
  public function exportDemandMx()
  {
      $inst_code = Request::param('instCode');
      $time = Request::param('time');
      $user_name = Request::param('user_name');
      $timeStart =  date('Y-m-d', strtotime($time[0]));
      $timeEnd =date('Y-m-d', strtotime($time[1]));
  
      if ($inst_code === '100001') {
          $inst_code_array = Db::table('inst')->select()->column('inst_code');
      } else if (strlen($inst_code) === 6) {
          $inst_code_array = Db::table('inst')->where('up_inst_code', $inst_code)->column('inst_code');
      } else {
          $inst_code_array = [$inst_code];
      }
      $inst_code_str = "'". implode("', '", $inst_code_array)."'";
      $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
          SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
          SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
          ) a INNER JOIN (
          SELECT * FROM demand_forecast where DATE(time)>? and DATE(time)<? and inst_code in (".$inst_code_str.") and user_name LIKE CONCAT('%',?, '%')
          ) b ON a.busi_id = b.busi_id 
          INNER JOIN materialinfo c ON b.material_code = c.material_code 
          ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
      $result1= Db::query($sql1, [$timeStart, $timeEnd,$user_name]);
      return json(['data' => $result1, 'total' => count($result1),'code' => 200]);
   }
    //导出需求预估汇总明细
  public function exportDemandMxTotal()
  {
      $inst_code = Request::param('instCode');
      $year = Request::param('year');
      if(empty($inst_code)){
        $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=?
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
        $result1= Db::query($sql1, [$year]);
        return json(['data' => $result1, 'total' => count($result1),'code' => 200]);
      }else{    
        $sql1 = "SELECT material_code,material_name,material_type,consumable, SUM(num) AS num FROM (
            SELECT b.material_code,b.material_name, b.num,c.material_type,c.consumable FROM (
            SELECT * FROM flow_approval WHERE flow_status = '5' GROUP BY busi_id 
            ) a INNER JOIN (
            SELECT * FROM demand_forecast_total where year=? and inst_code =?
            ) b ON a.busi_id = b.busi_id 
            INNER JOIN materialinfo c ON b.material_code = c.material_code 
            ) AS subquery GROUP BY material_code,material_name,material_type,consumable;";
        $result1= Db::query($sql1, [$year, $inst_code]);
        return json(['data' => $result1, 'total' => count($result1),'code' => 200]);
      }
   }

   
  //模糊查询客户姓名
  public function searchCustList()
  {
      $custom_name = Request::param('custom_name');
      $user_id = Request::param('user_id');
      $sql = "select a.*,b.employee_name from (SELECT * FROM custominfo where manager_code=? and custom_name LIKE CONCAT('%',?, '%'))a left join employee b on  a.manager_code=b.employee_code";
      $result= Db::query($sql, [$user_id,$custom_name]);
      return json(['data' => $result,'code' => 200]);
   } 
   //查询该市场部可申请的物资种类
   public function searchDepartWz()
   {
    
       $inst_code = Request::param('inst_code');
       $material_name = Request::param('material_name');
       $sql = "SELECT * FROM materialinfo where material_name LIKE CONCAT('%',?, '%') and inst_code=? and history_type='0'";
        $result= Db::query($sql, [$material_name, $inst_code]);
        return json(['data' => $result,'code' => 200]);
    }
    //提交物料申请
    public function submitWzApply()
    {
        $custom_name = Request::param('custom_name');
        $operator_name = Request::param('operator_name');
        $custom_license = Request::param('custom_license');
        $operator_telephone = Request::param('operator_telephone');
        $custom_address = Request::param('custom_address');
        $employee_name = Request::param('employee_name');
        $material_code = Request::param('material_code');
        $material_name = Request::param('material_name');
        $terminal_level = Request::param('terminal_level');
        $consumable = Request::param('consumable');
        $num = Request::param('num');
        $user_id = Request::param('user_id');
        $user_name = Request::param('user_name');
        $inst_code = Request::param('inst_code');
        $inst_name = Request::param('inst_name');
        $telephone = Request::param('telephone');
        $flow_no = Request::param('flow_no');
        $flow_title = Request::param('flow_title');
        $flow_node = Request::param('flow_node');
        $flow_node_name = Request::param('flow_node_name');
        $approval_name = Request::param('approval_name');
        $approval_content = Request::param('approval_content');
        $next_approval_id = Request::param('next_approval_id');
        $next_approval_name = Request::param('next_approval_name');
        $busi_id=Request::param('busi_id');
        $time =  date('Y-m-d H:i:s');
        Db::startTrans();
        try {
            //第一步没有busi_id插入，有则第一步退回后更新
            if(empty($busi_id)){
                $busi_id = $this->generateBusiId();
                    try {
                        Db::table('wz_apply')->insert([
                            'busi_id' =>$busi_id,
                            'custom_name' => $custom_name,
                            'operator_name' => $operator_name,
                            'custom_license' => $custom_license,
                            'operator_telephone' => $operator_telephone,
                            'custom_address' =>  $custom_address,
                            'employee_name' =>  $employee_name,
                            'material_code' =>  $material_code,
                            'material_name' =>  $material_name,
                            'terminal_level' =>  $terminal_level,
                            'consumable' =>  $consumable,
                            'num' =>  $num,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'inst_code' => $inst_code ,
                            'inst_name' => $inst_name ,
                            'telephone' => $telephone,
                            'time' =>  $time,
                        ]); 
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['message' => '数据库插入失败', 'code' => 300]);
                    }
            }else{
                //退回后再提交删除原来数据，新增新的数据
                if($flow_node==='1'){
                    DB::table('wz_apply')->where("busi_id",$busi_id)->delete();
                    try {
                        Db::table('wz_apply')->insert([
                            'busi_id' =>$busi_id,
                            'custom_name' => $custom_name,
                            'operator_name' => $operator_name,
                            'custom_license' => $custom_license,
                            'operator_telephone' => $operator_telephone,
                            'custom_address' =>  $custom_address,
                            'employee_name' =>  $employee_name,
                            'material_code' =>  $material_code,
                            'material_name' =>  $material_name,
                            'terminal_level' =>  $terminal_level,
                            'consumable' =>  $consumable,
                            'num' =>  $num,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'inst_code' => $inst_code ,
                            'inst_name' => $inst_name ,
                            'telephone' => $telephone,
                            'time' =>  $time,
                        ]); 
                    } catch (Exception $e) {
                        Db::rollback();
                        return json(['message' => '数据库插入失败', 'code' => 300]);
                    }
                }
            }
            try { 
                //发起插入当前和后一节点，之后审批更新当前节点，后一节点不为-99时插入后一节点
                $nextFlowNode= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->find();
                if($flow_node==='1'){
                    $currentFlowApproval= Db::table('flow_approval') ->where('busi_id', $busi_id)->where('flow_node',  $flow_node)->find();
                    if ($currentFlowApproval == null || count($currentFlowApproval) === 0) {
                        Db::table('flow_approval')->insert([
                            'busi_id' =>$busi_id,
                            'flow_no' => $flow_no,
                            'flow_title' => $flow_title,
                            'flow_node' => $flow_node,
                            'flow_node_name' => $flow_node_name,
                            'approval_id' => $user_id,
                            'approval_name' => $approval_name,
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time,
                            'show_type' => '1'
                        ]); 
                    } else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_node', $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_time' => $time
                        ]); 
                    }
                }else{
                    //如果当前节点不是最后一个节点
                    if($nextFlowNode['flow_node_next'] != '-99'){
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '1',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node)->where('show_type',  '1')
                        ->update([
                            'approve_status' => '0',
                            'flow_status' => '5',
                            'approval_content' => $approval_content,
                            'approval_time' => $time
                        ]); 
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)
                        ->update([
                            'flow_status' => '5'
                        ]);
                        $wzReslut= Db::table('materialinfo')->where('material_code', $material_code)->where('inst_code', $inst_code)->find();
                        if((int)$wzReslut['inventory_quantity']< (int)$num){
                            Db::rollback();
                            return json(['message' => '该物料库存不足', 'code' => 300]);
                        }else{
                            //定额标准
                            $standardReslut= Db::table('custominfo')->where('custom_license', $custom_license)->find();
                            if((int)$wzReslut['material_price'] * (int)$num >  (int)$standardReslut['remain_standard']){
                                Db::rollback();
                                return json(['message' => '该客户剩余定额标准不足', 'code' => 300]);
                            }else{
                                $remainNum=(int)$wzReslut['inventory_quantity']- (int)$num;
                                $remainStandardNum=(int)$standardReslut['remain_standard']- (int)$wzReslut['material_price'] * (int)$num;
                                try {
                                    Db::table('materialinfo')->where('material_code', $material_code)->where('inst_code', $inst_code)
                                    ->update([
                                        'inventory_quantity' => $remainNum
                                    ]);
                                    Db::table('custominfo')->where('custom_license', $custom_license)
                                    ->update([
                                        'remain_standard' => $remainStandardNum
                                    ]);
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库库存更新失败'], 'code' => 300]);
                                }
                                $userIdList=Db::table('wz_apply')->where('busi_id', $busi_id)->find();
                                try {
                                    Db::table('materialinfo_record')->insert([
                                        'material_code' => $material_code,
                                        'material_name' => $material_name,
                                        'quantity' => $num,
                                        'inventory_quantity' => $wzReslut['inventory_quantity'],
                                        'inst_code' => $inst_code,
                                        'type' => '库存认领',
                                        'allocate_time' => date('Y-m-d H:i:s'), // 使用当前时间
                                        'allocate_person' => $userIdList['user_id'], // 使用当前时间
                                    ]);
                                } catch (Exception $e) {
                                    Db::rollback();
                                    return json(['data' => ['message' => '数据库分配记录插入失败'], 'code' => 300]);
                                }
                            }
                        }
                    }
                }       
                if($nextFlowNode['flow_node_next'] != '-99'){
                    $flow_node_next = $nextFlowNode['flow_node_next'];
                    $nextList= Db::table('flow') ->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->find();
                    $flow_node_name_next = $nextList['flow_node_name'];
                    $nextResultList= Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)->select();
                    if($nextResultList  === null || count($nextResultList) === 0){
                    }else{
                        Db::table('flow_approval')->where('busi_id', $busi_id)->where('flow_no', $flow_no)->where('flow_node',  $flow_node_next)
                        ->update([
                            'show_type' => '0'
                        ]); 
                    }
                    Db::table('flow_approval')->insert([
                        'busi_id' =>$busi_id,
                        'flow_no' => $flow_no,
                        'flow_title' => $flow_title,
                        'flow_node' => $flow_node_next,
                        'flow_node_name' => $flow_node_name_next,
                        'approval_id' => $next_approval_id,
                        'approval_name' => $next_approval_name,
                        'approve_status' => '1',
                        'flow_status' => '1',
                        'approval_content' => '',
                        'show_type' => '1'
                    ]); 
                }
            } catch (Exception $e) {
                Db::rollback();
                return json(['message' => '数据库插入失败', 'code' => 300]);
            }
            Db::commit();
            if($flow_node==='1'){
                return json(['message' => '流程已提交', 'code' => 200]);
            }else{
                return json(['message' => '流程已审批', 'code' => 200]);
            }
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['message' => '操作失败', 'code' => 300]);
        }
    }
     //查询物资申请
    public function searchWzApply()
    {
        $busi_id=Request::param('busi_id');
        try {
            $resultList = Db::table('wz_apply')
            ->where('busi_id', $busi_id)
            ->select();
            return json(['data' => $resultList, 'code' => 200]);
        }  catch (Exception $e) {
            return json(['data' =>[],'message' => '查询物资申请失败', 'code' => 300]);
        }
    }
    //查询发放进度
    public function searchReviewProcess()
    {
        $procurement_time = Request::param('procurement_time');
        $inst_code = Request::param('instCode');
        $user_id = Request::param('user_id');
        if(empty($inst_code)){
            $employeeResult= Db::table('employee') ->where('employee_code', $user_id)->find();
            $inst_code_str = $employeeResult['inst_code'];
            $instSql='select inst_code from inst where up_inst_code=? union all select inst_code from inst where up_inst_code in (select inst_code from inst where up_inst_code=? and inst_code IS NOT NULL) and up_inst_code IS NOT NULL';
            $instResult= Db::query($instSql, [$inst_code_str, $inst_code_str]);
            $instCodeArray = array_column($instResult, 'inst_code');
            $inst_code_str1 = "'". implode("', '", $instCodeArray)."'";
            $sql = "select a.inst_code,a.inst_name, COALESCE(b.done_amount, 0) AS done_amount,
                COALESCE(c.is_consumable, 0) AS is_consumable,
                COALESCE(d.no_consumable, 0) AS no_consumable,
                COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                COALESCE(f.pt_price, 0) AS pt_price,
                COALESCE(g.jm_price, 0) AS jm_price,
                COALESCE(h.xd_price, 0) AS xd_price from(  
                (select inst_code,inst_name from inst where LENGTH(inst_code) = 7 and inst_code in (".$inst_code_str1."))a
                left join 
                (
                select sum(b.num*d.material_price) as done_amount,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id))b 
                left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                group by b.inst_code
                )b on a.inst_code=b.inst_code
                left join 
                (
                select sum(b.num*d.material_price) as is_consumable,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and  consumable='1' group by busi_id))b 
                left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )c on a.inst_code=c.inst_code
                left join 
                (
                select sum(b.num*d.material_price) as no_consumable,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )d on a.inst_code=d.inst_code
                left join
                (
                select  sum(b.num) as no_consumable_num,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )e on a.inst_code=e.inst_code
                left join (
                select   sum(b.num*d.material_price) as pt_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='普通')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )f on a.inst_code=f.inst_code
                left join (
                select   sum(b.num*d.material_price) as jm_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='加盟')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )g on a.inst_code=g.inst_code 
                left join (
                select   sum(b.num*d.material_price) as xd_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                and terminal_level='现代')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )h on a.inst_code=h.inst_code
                ) order by inst_code;"; 
            $result= Db::query($sql, [$procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time,$procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time]);
            return json(['data' => $result,'code' => 200]);
        }else{
            $sql = "select a.inst_code,a.inst_name, COALESCE(b.done_amount, 0) AS done_amount,
                COALESCE(c.is_consumable, 0) AS is_consumable,
                COALESCE(d.no_consumable, 0) AS no_consumable,
                COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                COALESCE(f.pt_price, 0) AS pt_price,
                COALESCE(g.jm_price, 0) AS jm_price,
                COALESCE(h.xd_price, 0) AS xd_price from(
                (select inst_code,inst_name from inst where LENGTH(inst_code) = 7 and inst_code=?)a
                left join 
                (
                select sum(b.num*d.material_price) as done_amount,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id))b 
                left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                group by b.inst_code
                )b on a.inst_code=b.inst_code
                left join 
                (
                select sum(b.num*d.material_price) as is_consumable,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  and consumable='1' group by busi_id))b 
                left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )c on a.inst_code=c.inst_code
                left join 
                (
                select sum(b.num*d.material_price) as no_consumable,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )d on a.inst_code=d.inst_code
                left join
                (
                select  sum(b.num) as no_consumable_num,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )e on a.inst_code=e.inst_code
                left join (
                select   sum(b.num*d.material_price) as pt_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                and terminal_level='普通')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )f on a.inst_code=f.inst_code
                left join (
                select   sum(b.num*d.material_price) as jm_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                and terminal_level='加盟')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )g on a.inst_code=g.inst_code 
                left join (
                select   sum(b.num*d.material_price) as xd_price,b.inst_code from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                and terminal_level='现代')b 
                left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )h on a.inst_code=h.inst_code
                );";
            $result= Db::query($sql, [$inst_code, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time]);
            return json(['data' => $result,'code' => 200]);
        }
        
    }
    //查询发放进度-客户经理
    public function searchReviewProcessKhjl()
    {
        $procurement_time = Request::param('procurement_time');
        $inst_code = Request::param('instCode');
        $role = Request::param('role');
        $user_id = Request::param('user_id');
        if($role ==='1'){
            if(!empty($user_id)){
                $sql = "select a.user_id,a.user_name, COALESCE(b.done_amount, 0) AS done_amount,
                    COALESCE(c.is_consumable, 0) AS is_consumable,
                    COALESCE(d.no_consumable, 0) AS no_consumable,
                    COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                    COALESCE(f.pt_price, 0) AS pt_price,
                    COALESCE(g.jm_price, 0) AS jm_price,
                    COALESCE(h.xd_price, 0) AS xd_price from(
                    (select user_id,user_name from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                     ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id) group by user_id
                    )a
                    left join 
                    (
                    select sum(b.num*d.material_price) as done_amount,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id))b 
                    left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                    group by b.user_id
                    )b on a.user_id=b.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as is_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and  consumable='1' group by busi_id))b 
                    left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )c on a.user_id=c.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as no_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.inst_code
                    )d on a.user_id=d.user_id
                    left join
                    (
                    select  sum(b.num) as no_consumable_num,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )e on a.user_id=e.user_id
                    left join (
                    select   sum(b.num*d.material_price) as pt_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                     and terminal_level='普通')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )f on a.user_id=f.user_id
                    left join (
                    select   sum(b.num*d.material_price) as jm_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                     and terminal_level='加盟')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )g on a.user_id=g.user_id 
                    left join (
                    select   sum(b.num*d.material_price) as xd_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='现代')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )h on a.user_id=h.user_id
                    );"; 
                $result= Db::query($sql, [$procurement_time,$user_id,$procurement_time,$procurement_time, $user_id,$procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time,$user_id,$procurement_time, $procurement_time, $user_id,$procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time, $user_id,$procurement_time]);
                return json(['data' => $result,'code' => 200]);
            }else{
                $sql = "select a.user_id,a.user_name, COALESCE(b.done_amount, 0) AS done_amount,
                    COALESCE(c.is_consumable, 0) AS is_consumable,
                    COALESCE(d.no_consumable, 0) AS no_consumable,
                    COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                    COALESCE(f.pt_price, 0) AS pt_price,
                    COALESCE(g.jm_price, 0) AS jm_price,
                    COALESCE(h.xd_price, 0) AS xd_price from(
                     (select user_id,user_name from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                     ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and inst_code=? group by busi_id) group by user_id
                    )a
                    left join 
                    (
                    select sum(b.num*d.material_price) as done_amount,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id))b 
                    left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                    group by b.user_id
                    )b on a.user_id=b.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as is_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  and consumable='1' group by busi_id))b 
                    left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )c on a.user_id=c.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as no_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )d on a.user_id=d.user_id
                    left join
                    (
                    select  sum(b.num) as no_consumable_num,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )e on a.user_id=e.user_id
                    left join (
                    select   sum(b.num*d.material_price) as pt_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='普通')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )f on a.user_id=f.user_id
                    left join (
                    select   sum(b.num*d.material_price) as jm_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='加盟')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )g on a.user_id=g.user_id 
                    left join (
                    select   sum(b.num*d.material_price) as xd_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='现代')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )h on a.user_id=h.user_id
                    );";
                    $result= Db::query($sql, [$procurement_time,$procurement_time,$inst_code, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time]);
                    return json(['data' => $result,'code' => 200]);
            }
        }else {
            if(!empty($user_id)){
                $sql = "select a.user_id,a.user_name, COALESCE(b.done_amount, 0) AS done_amount,
                    COALESCE(c.is_consumable, 0) AS is_consumable,
                    COALESCE(d.no_consumable, 0) AS no_consumable,
                    COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                    COALESCE(f.pt_price, 0) AS pt_price,
                    COALESCE(g.jm_price, 0) AS jm_price,
                    COALESCE(h.xd_price, 0) AS xd_price from(
                    (select user_id,user_name from wz_apply where SUBSTRING(time,1,4)=? and user_id=?  and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id) group by user_id
                    )a
                    left join 
                    (
                    select sum(b.num*d.material_price) as done_amount,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id))b 
                    left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                    group by b.user_id
                    )b on a.user_id=b.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as is_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and  consumable='1' group by busi_id))b 
                    left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )c on a.user_id=c.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as no_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.inst_code
                    )d on a.user_id=d.user_id
                    left join
                    (
                    select  sum(b.num) as no_consumable_num,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )e on a.user_id=e.user_id
                    left join (
                    select   sum(b.num*d.material_price) as pt_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='普通')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )f on a.user_id=f.user_id
                    left join (
                    select   sum(b.num*d.material_price) as jm_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='加盟')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )g on a.user_id=g.user_id 
                    left join (
                    select   sum(b.num*d.material_price) as xd_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='现代')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )h on a.user_id=h.user_id
                    );"; 
                $result= Db::query($sql, [$procurement_time,$user_id,$procurement_time,$procurement_time,$user_id, $procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time,$user_id,$procurement_time, $procurement_time,$user_id, $procurement_time, $procurement_time, $user_id,$procurement_time, $procurement_time,$user_id, $procurement_time]);
                return json(['data' => $result,'code' => 200]);
            }else{
                $sql = "select a.user_id,a.user_name, COALESCE(b.done_amount, 0) AS done_amount,
                    COALESCE(c.is_consumable, 0) AS is_consumable,
                    COALESCE(d.no_consumable, 0) AS no_consumable,
                    COALESCE(e.no_consumable_num, 0) AS no_consumable_num,
                    COALESCE(f.pt_price, 0) AS pt_price,
                    COALESCE(g.jm_price, 0) AS jm_price,
                    COALESCE(h.xd_price, 0) AS xd_price from(
                    (select user_id,user_name from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and inst_code=? group by busi_id) group by user_id
                    )a
                    left join 
                    (
                    select sum(b.num*d.material_price) as done_amount,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id))b 
                    left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                    group by b.user_id
                    )b on a.user_id=b.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as is_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  and consumable='1' group by busi_id))b 
                    left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )c on a.user_id=c.user_id
                    left join 
                    (
                    select sum(b.num*d.material_price) as no_consumable,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )d on a.user_id=d.user_id
                    left join
                    (
                    select  sum(b.num) as no_consumable_num,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                    left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )e on a.user_id=e.user_id
                    left join (
                    select   sum(b.num*d.material_price) as pt_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='普通')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )f on a.user_id=f.user_id
                    left join (
                    select   sum(b.num*d.material_price) as jm_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='加盟')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )g on a.user_id=g.user_id 
                    left join (
                    select   sum(b.num*d.material_price) as xd_price,b.user_id from ( 
                    select * from wz_apply where SUBSTRING(time,1,4)=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? group by busi_id)
                    and terminal_level='现代')b 
                    left join materialinfo  d on b.material_code=d.material_code and b.inst_code=d.inst_code
                    group by b.user_id
                    )h on a.user_id=h.user_id
                    );";
                $result= Db::query($sql, [$procurement_time,$procurement_time,$inst_code, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time, $procurement_time]);
                return json(['data' => $result,'code' => 200]);
            } 
        }
    }
    
    //查询发放进度-客户
    public function searchReviewProcessKh()
    {
        $procurement_time = Request::param('procurement_time');
        $user_id = Request::param('user_id');
        $custom_license = Request::param('custom_license');
        if(empty($custom_license)){
            $sql = "select a.custom_license,a.custom_name, COALESCE(b.done_amount, 0) AS done_amount,
                COALESCE(c.is_consumable, 0) AS is_consumable,
                COALESCE(d.no_consumable, 0) AS no_consumable,
                COALESCE(e.quota_standard, 0) AS quota_standard,
                COALESCE(e.remain_standard, 0) AS remain_standard from(
                (select custom_name,custom_license from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id) group by custom_license
                )a
                left join 
                (
                select sum(b.num*d.material_price) as done_amount,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id))b 
                left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                group by b.custom_license
                )b on a.custom_license=b.custom_license
                left join 
                (
                select sum(b.num*d.material_price) as is_consumable,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and  consumable='1' group by busi_id))b 
                left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.custom_license
                )c on a.custom_license=c.custom_license
                left join 
                (
                select sum(b.num*d.material_price) as no_consumable,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )d on a.custom_license=d.custom_license
                left join 
                (
                    select custom_license,quota_standard,remain_standard from custominfo group by  custom_license
                )e on a.custom_license=e.custom_license
                );";
                $result= Db::query($sql, [$procurement_time,$user_id,$procurement_time, $procurement_time,$user_id,$procurement_time, $procurement_time,$user_id,$procurement_time, $procurement_time,$user_id,$procurement_time]);
                return json(['data' => $result,'code' => 200]);
        } else{
            $sql = "select a.custom_license,a.custom_name, COALESCE(b.done_amount, 0) AS done_amount,
                COALESCE(c.is_consumable, 0) AS is_consumable,
                COALESCE(d.no_consumable, 0) AS no_consumable,
                COALESCE(e.quota_standard, 0) AS quota_standard,
                COALESCE(e.remain_standard, 0) AS remain_standard from(
                (select custom_name,custom_license from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and custom_license=? and busi_id in
                    ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id) group by custom_license
                )a
                left join 
                (
                select sum(b.num*d.material_price) as done_amount,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and custom_license=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=?  group by busi_id))b 
                left join materialinfo d on b.material_code=d.material_code and b.inst_code=d.inst_code 
                group by b.custom_license
                )b on a.custom_license=b.custom_license
                left join 
                (
                select sum(b.num*d.material_price) as is_consumable,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and custom_license=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and  consumable='1' group by busi_id))b 
                left join (select * from materialinfo where consumable='1') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.custom_license
                )c on a.custom_license=c.custom_license
                left join 
                (
                select sum(b.num*d.material_price) as no_consumable,b.custom_license from ( 
                select * from wz_apply where SUBSTRING(time,1,4)=? and user_id=? and custom_license=? and busi_id in
                ( select busi_id from flow_approval where flow_status='5'and (flow_no='3' or flow_no='4') and SUBSTRING(approval_time,1,4)=? and consumable='0' group by busi_id))b 
                left join (select * from materialinfo where consumable='0') d on b.material_code=d.material_code and b.inst_code=d.inst_code
                group by b.inst_code
                )d on a.custom_license=d.custom_license
                left join 
                (
                    select custom_license,quota_standard,remain_standard from custominfo group by  custom_license
                )e on a.custom_license=e.custom_license
                );";
                $result= Db::query($sql, [$procurement_time,$user_id,$custom_license,$procurement_time, $procurement_time,$user_id,$custom_license,$procurement_time, $procurement_time,$user_id,$custom_license,$procurement_time, $procurement_time,$user_id,$custom_license,$procurement_time]);
                return json(['data' => $result,'code' => 200]);
        }
    }

    //判断时间开关
    public function judgeDemandTime()
    {
        
        $name= Request::param('name');
        $result = Db::table('demand_time_control')->where('name', $name)->find();
        return json(['data' => $result['type'],'code' => 200]);
    }
    //开启关不时间开关
    public function updateDemandTime()
    {
        $type = Request::param('type');
        $name= Request::param('name');
        try {
            Db::table('demand_time_control')->where('name', $name)
            ->update([
                'type' => $type
            ]);
            return json(['message' => '操作成功','code' => 200]);
        } catch (Exception $e) {
            // 记录插入错误
            return json(['message' => '操作失败', 'code' => 300]);
        }
    }
    //导出机构信息
    public function exportInst()
    {
        $searchList = Db::table('inst')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //导出员工信息
    public function exportEmp()
    {
        $searchList = Db::table('employee')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    //导出客户信息
    public function exportCust()
    {
        $searchList = Db::table('custominfo')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
        }
    }
    
    //导入机构
    public function importInst()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');

        // 开始事务
        Db::startTrans();

        try {
            try {
                DB::table('inst')->where("1=1")->delete();
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库删除失败'], 'code' => 300]);
            }
            foreach ($data as $item) {
                // 提取每条数据的字段
                $inst_code = $item['机构编码'];
                $inst_name = $item['机构名称'];
                $up_inst_code = $item['上级机构'];
                $up_inst_name = $item['上级机构名称'];
                $start_time = $item['创建时间'];
                $end_time = $item['撤销时间'];
                try {
                    Db::table('inst')->insert([
                        'inst_code' => $inst_code,
                        'inst_name' => $inst_name,
                        'up_inst_code' => $up_inst_code,
                        'up_inst_name' => $up_inst_name,
                        'start_time' => $start_time,
                        'end_time' =>  $end_time
                    ]); 
                } catch (Exception $e) {
                    Db::rollback();
                    return json(['data' => ['message' => '数据库插入失败'], 'code' => 300]);
                }
            }
            try {
                $sys_record_data = [
                    'user_id' => $userId,
                    'type' => '上传机构表',
                    'time' => date('Y-m-d H:i:s')
                ];
                Db::table('sysrecord')->insert($sys_record_data);
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
            }
            // 提交事务
            Db::commit();
            return json(['data' => ['message' => '机构导入成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    //导入员工
    public function importEmp()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');

        // 开始事务
        Db::startTrans();

        try {
            try {
                DB::table('employee')->where("1=1")->delete();
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库删除失败'], 'code' => 300]);
            }
            foreach ($data as $item) {
                // 提取每条数据的字段
                $employee_code = $item['员工编号'];
                $employee_name = $item['员工姓名'];
                $type = $item['权限类型（岗位性质）'];
                $inst_code = $item['所属机构编码'];
                $inst_name = $item['所属机构名称'];
                $telephone = $item['联系电话'];
                $status = $item['员工状态'];
                $gender = $item['员工性别'];
                $age = $item['员工年龄'];
                try {
                    Db::table('employee')->insert([
                        'employee_code' => $employee_code,
                        'employee_name' => $employee_name,
                        'type' => $type,
                        'inst_code' => $inst_code,
                        'inst_name' => $inst_name,
                        'telephone' =>  $telephone,
                        'status' =>  $status,
                        'gender' =>  $gender,
                        'age' =>  $age
                    ]); 
                } catch (Exception $e) {
                    Db::rollback();
                    return json(['data' => ['message' => '数据库插入失败'], 'code' => 300]);
                }
            }
            try {
                $sys_record_data = [
                    'user_id' => $userId,
                    'type' => '上传员工表',
                    'time' => date('Y-m-d H:i:s')
                ];
                Db::table('sysrecord')->insert($sys_record_data);
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
            }
            // 提交事务
            Db::commit();
            return json(['data' => ['message' => '员工导入成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    //导入客户信息
    public function importCust()
    {
        $data = Request::param('data');
        $userId = Request::param('userId');

        // 开始事务
        Db::startTrans();
        try {
            try {
                DB::table('custominfo')->where("1=1")->delete();
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库删除失败'], 'code' => 300]);
            }
            foreach ($data as $item) {
                // 提取每条数据的字段
                $custom_code = $item['客户编码'];
                $custom_license = $item['许可证号'];
                $manager_code = $item['客户经理编码'];
                $custom_name = $item['客户（企业）名称'];
                $custom_address = $item['客户经营地址'];
                $longitude = $item['GIS经度'];
                $latitude = $item['GIS纬度'];
                $starting_time = $item['开始经营时间'];
                $ending_time = $item['结束经营时间'];
                $terminal_level = $item['客户终端层级'];
                $custom_classification = $item['客户分类（档位）'];
                $credit_rating = $item['信用等级'];
                $business_status = $item['经营状态'];
                $long_term_non_delivery = $item['是否长期不要货'];
                $operator_name = $item['负责人姓名'];
                $operator_telephone = $item['经营者联系电话'];
                $market_type = $item['市场类型（城网、农网）'];
                $quota_standard = $item['定额标准'];
                $remain_standard = $item['剩余定额标准'];
                $region = $item['所属区域'];
                $whitelist_customer = $item['是否白名单客户（1是0否）'];
                try {
                    Db::table('custominfo')->insert([
                        'custom_code' => $custom_code,
                        'custom_license' => $custom_license,
                        'manager_code' => $manager_code,
                        'custom_name' => $custom_name,
                        'custom_address' => $custom_address,
                        'longitude' =>  $longitude,
                        'latitude' =>  $latitude,
                        'starting_time' =>  $starting_time,
                        'ending_time' =>  $ending_time,
                        'terminal_level' =>  $terminal_level,
                        'custom_classification' =>  $custom_classification,
                        'credit_rating' =>  $credit_rating,
                        'business_status' =>  $business_status,
                        'long_term_non_delivery' =>  $long_term_non_delivery,
                        'operator_name' =>  $operator_name,
                        'operator_telephone' =>  $operator_telephone,
                        'market_type' =>  $market_type,
                        'quota_standard' =>  $quota_standard,
                        'remain_standard' =>  $remain_standard,
                        'region' =>  $region,
                        'whitelist_customer' =>  $whitelist_customer
                    ]); 
                } catch (Exception $e) {
                    Db::rollback();
                    return json(['data' => ['message' => '数据库插入失败'], 'code' => 300]);
                }
            }
            try {
                $sys_record_data = [
                    'user_id' => $userId,
                    'type' => '上传客户表',
                    'time' => date('Y-m-d H:i:s')
                ];
                Db::table('sysrecord')->insert($sys_record_data);
            } catch (Exception $e) {
                Db::rollback();
                return json(['data' => ['message' => '数据库记录插入失败'], 'code' => 300]);
            }
            // 提交事务
            Db::commit();
            return json(['data' => ['message' => '客户信息导入成功'], 'code' => 200]);
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['data' => ['message' => '操作失败: ' . $e->getMessage()], 'code' => 300]);
        }
    }
    
    //查询机构表
    public function searchInst()
    {
        $instName = Request::param('instName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList = Db::table('inst')
        ->where('inst_name', 'like', '%' . $instName . '%')->order('inst_code')
        ->page($page, $pageSize)->select();
        $total =  Db::table('inst')->where('inst_name', 'like', '%' . $instName . '%')
        ->count();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    //查询员工表
    public function searchEmp()
    {
        $empName = Request::param('empName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList = Db::table('employee')
        ->where('employee_name', 'like', '%' . $empName . '%')->order('employee_code')
        ->page($page, $pageSize)->select();
        $total =  Db::table('employee') ->where('employee_name', 'like', '%' . $empName . '%')
        ->count();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    //查询客户信息表
    public function searchCust()
    {
        $custName = Request::param('custName');
        $page = Request::param('page', 1); 
        $pageSize = Request::param('pageSize', 10);
        $searchList = Db::table('custominfo')
        ->where('custom_name', 'like', '%' . $custName . '%')->order('custom_code')
        ->page($page, $pageSize)->select();
        $total =  Db::table('custominfo') ->where('custom_name', 'like', '%' . $custName . '%')
        ->count();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [],'total' => 0, 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'total' => $total, 'code' => 200]);
        }
    }
    
    //查询延期库存记录
    public function searchDelayMessage()
    {
        $instCode = Request::param('instCode');
        $creation_time = date('Y-m-d');
        $new_date = date('Y-m-d', strtotime("+20 days", strtotime($creation_time)));
        $sql = "select a.*,b.inst_name from (
        select * from materialinfo where inst_code in (
        select inst_code from inst where inst_code=? 
        union all 
        select inst_code from inst where inst_code in (select inst_code  from inst where up_inst_code=?)
         union all 
        select inst_code from inst where inst_code in (select inst_code from inst where up_inst_code in (select inst_code  from inst where up_inst_code=?))) and history_type='0' and end_time <=?
        ) a left join  inst b on a.inst_code=b.inst_code";
        $searchList= Db::query($sql, [$instCode,$instCode,$instCode,$new_date]);
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList,'code' => 200]);
        }
    }
}


