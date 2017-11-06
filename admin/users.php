<?php

require_once '../functions.php';
//检查用户登录信息
xiu_get_current_user();
//添加用户
function add_user () {
  if (empty($_POST['email'])) {
    $GLOBALS['message'] = '请填写邮箱';
    $GLOBALS['success'] = false;
    return;
  }
   if (empty($_POST['slug'])) {
    $GLOBALS['message'] = '请填写别名';
    $GLOBALS['success'] = false;
    return;
  }
   if (empty($_POST['nickname'])) {
    $GLOBALS['message'] = '请填写昵称';
    $GLOBALS['success'] = false;
    return;
  }
   if (empty($_POST['password'])) {
    $GLOBALS['message'] = '请填写密码';
    $GLOBALS['success'] = false;
    return;
  }
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  //insert into users values (null, 'qq', '123@qq.com', '121212', '管', '/static/uploads/avatar.jpg', null, 'activated');
  $rows = xiu_execute("insert into users values (null, '{$slug}', '{$email}', '{$password}', '{$nickname}', '/static/uploads/avatar.jpg', null, 'activated');");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败' : '添加成功';
}

if (empty($_GET['id'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
  }
} else {
  $current_edit_user = xiu_fetch_one('')
}

//查询全部用户信息
// select * from users;
$users = xiu_fetch_all('select * from users;');

function convert_status ($status) {
  $dict = array(
    'unactivated' => '未激活',
    'activated' => '激活',
    'forbidden' => '禁止',
    'trashed' => '回收站'
    );
  // return isset($dict[$status]) ? $dict[$status] : '未知';
    return isset($dict[$status]) ? $dict[$status] : '未知';
}

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
       <?php if (isset($message)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
            <strong>成功！</strong> <?php echo $message; ?>
          </div>
          <?php else: ?>
          <div class="alert alert-danger">
            <strong>错误！</strong> <?php echo $message; ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="user-delete.php" style="display: none" id="btn_delete">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>       
              <?php foreach ($users as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                  <td class="text-center"><img class="avatar" src="/static/assets/img/default.png"></td>
                  <td><?php echo $item['email']; ?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td><?php echo $item['nickname']; ?></td>
                  <td><?php echo convert_status($item['status']); ?></td>
                  <td class="text-center">
                    <a href="/admin/users.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/user-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>  
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function ($) {

      var $tbodyCheckboxs = $('tbody input')
      var $btnDelete = $('#btn_delete')
      var allCheckeds = []

      $tbodyCheckboxs.on('change', function () {
        var id = $(this).data('id')
        if ($(this).prop('checked')) {
          allCheckeds.push(id)
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1)
        }

        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search', '?id=' + allCheckeds)
      })

    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
