<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class ExerciseController extends HomeController {

	/* 用户中心首页 */
        public function exc_common(){
            $this->display();
	}
	
        public function exc_filter(){
            $list = M("Mainmuscletype");
            $list = $list->getField('id, name');
            $this->assign("_list", $list);
            
            $list2 = M("Exercisetype");
            $list2 = $list2->getField('id, name');
            $this->assign("_list2", $list2);
            
            $list3 = M("Equiptype");
            $list3 = $list3->getField('id, name');
            $this->assign("_list3", $list3);
            
            $list4 = M("Forcetype");
            $list4 = $list4->getField('id, name');
            $this->assign("_list4", $list4);
            
            $list5 = M("Sporttype");
            $list5 = $list5->getField('id, name');
            $this->assign("_list5", $list5);
            
            $list6 = M("Leveltype");
            $list6 = $list6->getField('id, name');
            $this->assign("_list6", $list6);
            $this->display();
	}
        
        public function search()
        {
            $filter1 = I('filter_1');
            $filter1 = substr($filter1,0,strlen($filter1)-1);
            $map['mainmuscleID'] = array('in', $filter1);
            
            $filter2 = I('filter_2');
            
            $filter2 = substr($filter2,0,strlen($filter2)-1);
            $map['exercisetypeID'] = array('in', $filter2);
            
            $filter3 = I('filter_3');
            $filter3 = substr($filter3,0,strlen($filter3)-1);
            $map['equiptypeID'] = array('in', $filter3);
            
            $filter4 = I('filter_4');
            $filter4 = substr($filter4,0,strlen($filter4)-1);
            $map['forcetypeID'] = array('in', $filter4);
            
            $filter5 = I('filter_5');
            $filter5 = substr($filter5, 0,strlen($filter5)-1);
            $map['sporttypeID'] = array('in', $filter5);
            
            $filter6 = I('filter_6');
            $filter6 = substr($filter6,0,strlen($filter6)-1);
            $map['levelID'] = array('in', $filter6);
            
            //$exercise = D('Exercise');
            $exercise = M("Exercise");
            /*$info = $exercise->table('Exercise exc, Mainmuscletype t1')
                    ->where ( 'exc.mainmuslceID = t1.id')
                    ->field('exc.ename as ename, t1.name as mname, exc.sex, exc.rating')
                    ->select();*/
            
             $prefix   = C('DB_PREFIX');
            $l_table  = $prefix.('exercise');
            $r_table1  = $prefix.('mainmuscletype');
            $r_table2  = $prefix.('exercisetype');
            $r_table3  = $prefix.('equiptype');
            $r_table4  = $prefix.('forcetype');
            $r_table5  = $prefix.('sporttype');
            $r_table6  = $prefix.('leveltype');
            
            $info = M() ->table($l_table.' exc')
                       ->where( $map )
                       ->join ( $r_table1.' t1 on exc.mainmuscleID = t1.id' )
                    ->join ( $r_table2.' t2 on exc.exercisetypeID = t2.id' )
                    ->join ( $r_table3.' t3 on exc.equiptypeID = t3.id' )
                    ->join ( $r_table4.' t4 on exc.forcetypeID = t4.id' )
                    ->join ( $r_table5.' t5 on exc.sporttypeID = t5.id' )
                    ->join ( $r_table6.' t6 on exc.levelID = t6.id' )
                    ->field('exc.ename as ename, t1.name as mtname, t2.name as etname, t3.name as eqtname,'
                            . 't4.name as ftname, t5.name as stname, exc.eid,t6.name as ltname, exc.sex, exc.rating')
                    ->select();
        
            $data["status"] = 0;
            if(count($info) > 0)
            {
                $data["status"] = 1;
                $data["info"] = $info;
            }
            $this->ajaxReturn($data, 'json');
        }

    public function exc_all(){
        $list = M('exercisecomment')->where(array('eid'=>$_GET['eid']));
        $list = $this->lists('exercisecomment', null, null,null, null);
        int_to_string($list);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 评论添加
     */
    public function add_review(){
        if(IS_AJAX){
            $data = array(
                'eid'=>I('eid'),
                'uid'=>is_login(),
                'replyid'=>'0',
                'content'=>I('content'),
                'create_time'=>date('Y-m-d H:i:s',time()),
                'status'=>'1',
                'viewnum'=>''
            );
            if($id=M('exercisecomment')->add($data)){
                $result = review_comment($data['uid']);
                $json = json_encode(array('status'=>1,'cid'=>$id,'name'=>$result['username'],'content'=>$data['content'],'create_time'=>$data['create_time'],'uid'=>$data['uid']));
                echo $json;
            } 
        }
    }

    /**
     * 评论回复
     */
    public function reply_save(){
       if(IS_AJAX){
            $data = array(
                'eid'=>'1',
                'uid'=>I('uid'),
                'replyid'=>I('cid'),
                'content'=>I('content'),
                'create_time'=>date('Y-m-d H:i:s',time()),
                'status'=>'1',
                'viewnum'=>''
            );
            if(M('exercisecomment')->add($data)){
                $result = review_comment($data['uid']);
                $json = json_encode(array('status'=>1,'cid'=>$data['replyid'],'name'=>$result['username'],'content'=>$data['content'],'create_time'=>$data['create_time']));
                echo $json;
            } 
        }
    }

}
