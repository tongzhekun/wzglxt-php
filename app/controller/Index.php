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

    //上传卷烟参数
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

    //下载卷烟参数
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
    //上传客户参数
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

     //下载客户参数
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
