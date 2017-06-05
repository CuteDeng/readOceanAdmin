<?php
/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 17/3/20
 * Time: 上午10:55
 */

namespace Home\Controller;

use Component\CommonController;
use think\console\command\make\Model;

class testController extends CommonController
{
    public function index()
    {
        $html = array('http://ro.bnuz.edu.cn/tinyread/book/026a0c79-7ef0-4ee9-8957-eadc50b1300a/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/06856e86-bdf7-4e9b-94e9-1a15b383a726/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/2971d80b-84fe-48cb-a921-8ae3814d69f4/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/2e944155-b5d2-4e6e-bbdb-5df1fffcf25b/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/364a6c66-a2ce-425c-ad78-f7ca044c4f41/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/4aa3fccd-d816-428b-aa50-1bf9e5bafa32/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/4c8750e5-bda4-447f-87e6-136a0020664e/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/516455e6-64de-47d3-9812-0f34accf54c9/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/5917bd81-2daa-4cef-a940-9fec3a070f8c/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/635d5a23-ca2b-4cf3-9463-094813caa32c/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/6835313f-275b-469d-bf33-108a71179ab0/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/71a891e0-3c5a-4d95-bc3d-ef39683ec730/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/74142d97-f8be-45b5-811d-fccaa3eab798/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/8442145a-ee3a-4c3d-9bc1-66a8a4b76336/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/8f15680c-f960-4a0f-89bb-71a48908ba55/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/984dec59-538c-423a-a9dc-0351a3c89243/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/998108bd-d62c-4e13-8c89-f3c8c07c9fac/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/9c70022f-0a16-4599-ac0f-5c7698f38432/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/9cf63065-8082-4080-96f0-ea0859fdeb73/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/d2b55cc2-30ba-411b-809f-af52ce0dd5ce/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/dab458e6-75e1-4f1a-b189-42a4f7feafc1/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/ecb94f4a-a55c-4f61-a593-18d533dd44de/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/ed29c042-6854-48c3-974d-19737a6d59b3/index.html',
            'http://ro.bnuz.edu.cn/tinyread/book/f5023f6d-acb6-4472-a61d-5790935222be/index.html',);
        $flag = 0;
        $model = new \Think\Model();
        $model->startTrans();
        foreach ($html as $value) {
            $bookid = explode("/", $value)[5];
            $con = M('books')->where('id=\'' . $bookid . "'")->field('name,suit')->find();
            $con['type'] = "tinyread_type_book";
            $con['url'] = $value;
            $data['id'] = getUid();
            while (1) {
                if (!M('tinyread')->where($data)->select()) {
                    break;
                } else {
                    $data['id'] = getUid();
                }
            }
            $con['id'] = $data['id'];
            if (M('tinyread')->add($con)) {
                $save['guideUrl'] = $data['id'];
                if (M('books')->where('id=\'' . $bookid . "'")->save($save)) {
                    $flag += 1;
                }
            }
        }
        if ($flag == count($html)) {
            $model->commit();
        } else {
            $model->rollback();
            echo 13;
        }
    }

    public function postNumber()
    {
        $post = M('forum_post')->group('topicId')->field('count(*) as count,topicId')->select();
        foreach ($post as $value) {
            $con['topicId'] = $value['topicid'];
            $arr['postNum'] = $value['count'];
            $flag = M('forum_topic')->where($con)->save($arr);
            if ($flag === 0) {
                echo 1;
            }
        }
    }

    public function thumberNumber()
    {
        $good = M('forum_post')->group('topicId')->field('sum(thumbUpNumbers) as summary,topicId')->select();
        foreach ($good as $value) {
            dump($value);
            $con['topicId'] = $value['topicid'];
            $arr['thumbNumbers'] = $value['summary'];
            $flag = M('forum_topic')->where($con)->save($arr);
            if ($flag == 0) {
                echo 1;
            }
        }
    }

    public function teststr()
    {
        echo strtotime(date('Y-m-d 23:59:59', strtotime('2017-05-09')));
    }
}