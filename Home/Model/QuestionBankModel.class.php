<?php
/*/**
 * Created by PhpStorm.
 * User: Chan
 * Date: 16/12/21
 * Time: 下午1:20
 */
namespace Home\Model;

use Think\Model;

class QuestionBankModel extends Model
{
    protected $trueTableName = 'true_or_false_questions';

    //$data数据
    public function addtrueorfalse($book, $data)
    {
        $data['bookId'] = $book;
        $con['id'] = getUid();
        while (1) {
            $flag = M('true_or_false_questions')->where($con)->select();
            if ($flag) {
                $con['id'] = getUid();
            } else {
                $data['id'] = $con['id'];
                break;
            }
        }
        switch ($data['diff']) {
            case '简单':
                $data['difficultyType'] = 'book_question_diff_jiandan';
                break;
            case '一般':
                $data['difficultyType'] = 'book_question_diff_zhongji';
                break;
            case '困难':
                $data['difficultyType'] = 'book_question_diff_kunnan';
                break;
        }
        switch ($data['answer']) {
            case '对':
                $data['answer'] = 1;
                break;
            case '错':
                $data['answer'] = 0;
                break;
        }

        if (M('true_or_false_questions')->add($data)) {
            return 1;//说明成功了
        } else {
            return 0;
        }
    }

    public function addsingle($book, $data)
    {
        $data['bookId'] = $book;
        $con['id'] = getUid();
        while (1) {
            $flag = M('single_choice_questions')->where($con)->select();
            if ($flag) {
                $con['id'] = getUid();
            } else {
                $data['id'] = $con['id'];
                break;
            }
        }
        switch ($data['diff']) {
            case '简单':
                $data['difficultyType'] = 'book_question_diff_jiandan';
                break;
            case '一般':
                $data['difficultyType'] = 'book_question_diff_zhongji';
                break;
            case '困难':
                $data['difficultyType'] = 'book_question_diff_kunnan';
                break;
        }
        if (M('single_choice_questions')->add($data)) {
            return 1;//说明成功了
        } else {
            return 0;
        }
    }

    public function addmultiple($book, $data)
    {
        $data['bookId'] = $book;
        $con['id'] = getUid();
        while (1) {
            $flag = M('multiple_choice_questions')->where($con)->select();
            if ($flag) {
                $con['id'] = getUid();
            } else {
                $data['id'] = $con['id'];
                break;
            }
        }
        switch ($data['diff']) {
            case '简单':
                $data['difficultyType'] = 'book_question_diff_jiandan';
                break;
            case '一般':
                $data['difficultyType'] = 'book_question_diff_zhongji';
                break;
            case '困难':
                $data['difficultyType'] = 'book_question_diff_kunnan';
                break;
        }
        if (M('multiple_choice_questions')->add($data)) {
            return 1;//说明成功了
        } else {
            return 0;
        }
    }
}