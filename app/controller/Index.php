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
                 $data['message'] = '烟草参数数据表无数据，查询失败';
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


    public function download()
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="template.xlsx"');
        // 这里假设模板文件为 template.xlsx，可以根据实际情况修改
        readfile('template.xlsx');
    }
}