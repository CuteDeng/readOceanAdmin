<?php
/* 无限级分类 */
/* 有啥参数自己添加修改$value */
namespace Component;
/* 无限级分类 表中只要有2个字段就行id，fatherId*/
class Classify
{
    static public $treeList = array(); //存放无限分类结果如果一页面有多个无限分类可以使用 Tool::$treeList = array(); 清空
    static public $nodeList = array(); //存放无限分类结果如果一页面有多个无限分类可以使用 Tool::$treeList = array(); 清空
    static public $fatherNodeList = array(); //存放无限分类结果如果一页面有多个无限分类可以使用 Tool::$treeList = array(); 清空

    /**
     * 无限级分类
     * @access public
     * @param Array $data //数据库里获取的结果集
     * @param Int $pid
     * @param Int $count //第几级分类
     * @return Array $treeList
     */
    static public function tree($data, $pid = 0, $count = 1)
    {
        foreach ($data as $key => $value) {
            if ($value['fatherid'] == $pid) {
                $value['level'] = $count;
                self::$treeList [] = $value;
                unset($data[$key]);
                self::tree($data, $value['pid'], $count + 1);
            }
        }
        return self::$treeList;
    }

    /**
     * 无限级分类获取当前节点下的所有节点，包括本身哦
     * @access public
     * @param Array $data //数据库里获取的结果集
     * @param Int $id //某个节点
     * @return Array $nodeList
     */
    static public function getAllNode($data, $id)
    {
        foreach ($data as $key => $value) {
            if ($value['fatherid'] == $id) {
                self::$nodeList [] = $value;
                unset($data[$key]);
                self::getAllNode($data, $value['pid']);
            }
            if ($value['id'] == $id) {
                self::$nodeList [] = $value;
            }
        }
        return self::$nodeList;
    }

    /**
     * 无限级分类获取当前节点的所有父节点，包括本身哦
     * @access public
     * @param Array $data //数据库里获取的结果集
     * @param Int $id //某个节点
     * @return Array $nodeList
     */
    static public function getAllFaNode($data, $fatherid)
    {
        foreach ($data as $key => $value) {
            if ($value['pid'] == $fatherid) {
                self::$fatherNodeList [] = $value;
                unset($data[$key]);
                self::getAllFaNode($data, $value['fatherid']);
            }
        }
        return self::$fatherNodeList;
    }

}