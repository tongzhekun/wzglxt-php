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
        $institutions = Db::table('inst')->select();
        // 构建机构树的函数
        function buildInstitutionTree($institutions) {
            $tree = [];
            foreach ($institutions as $institution) {
                if ($institution["up_inst_code"] == "") {
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

    //查询现有库存记录
    public function searchCk()
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
        $searchList = Db::table('inst')->whereRaw('LENGTH(inst_code) = 7')->order('inst_code')->select();
        if ($searchList == null || count($searchList) === 0) {
            $data['message'] = 'success';
            return json(['data' => [], 'code' => 200]);
        } else {
            $data['message'] = 'success';
            return json(['data' => $searchList, 'code' => 200]);
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
        ->field(['material_code', 'material_name','inventory_quantity']) // 指定需要的字段
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
        $searchList = Db::table('materialinfo')->where('inst_code', $instCode)->where('history_type', '1')->where('material_name', 'like', '%' . $materialName . '%')->select();
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
}
