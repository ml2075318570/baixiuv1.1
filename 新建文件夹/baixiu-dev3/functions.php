<?php

require_once 'config.php';

/**
 * 封装大家公用的函数
 */
session_start();

// 定义函数时一定要注意：函数名与内置函数冲突问题
// JS 判断方式：typeof fn === 'function'
// PHP 判断函数是否定义的方式： function_exists('get_current_user')

/**
 * 获取当前登录用户信息，如果没有获取到则自动跳转到登录页面
 * @return [type] [description]
 */
function xiu_get_current_user () {
  if (empty($_SESSION['current_login_user'])) {
    // 没有当前登录用户信息，意味着没有登录
    header('Location: /admin/login.php');
    exit(); // 没有必要再执行之后的代码
  }
  return $_SESSION['current_login_user'];
}

/**
 * 通过一个数据库查询获取数据
 */
function xiu_fetch ($sql) {
  $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
  if (!$conn) {
    exit('连接失败');
  }

  $query = mysqli_query($conn, $sql);
  if (!$query) {
    // 查询失败
    return false;
  }

  while ($row = mysqli_fetch_assoc($query)) {
    $result[] = $row;
  }

  return $result;
}
