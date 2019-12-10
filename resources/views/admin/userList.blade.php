@extends('admin.layouts')
@section('css')
	<link href="/assets/global/vendor/bootstrap-table/bootstrap-table.min.css" type="text/css" rel="stylesheet">
@endsection
@section('content')
	<div class="page-content container-fluid">
		<div class="panel">
			<div class="panel-heading">
				<h2 class="panel-title">用户列表</h2>
				<div class="panel-actions">
					<button class="btn btn-outline-default" onclick="exportSSJson()">
						<i class="icon wb-download" aria-hidden="true"></i>导出JSON
					</button>
					<button class="btn btn-outline-default" onclick="batchAddUsers()">
						<i class="icon wb-plus" aria-hidden="true"></i>批量生成
					</button>
					<a href="/admin/addUser" class="btn btn-outline-default">
						<i class="icon wb-user-add" aria-hidden="true"></i>添加用户
					</a>
				</div>
			</div>
			<div class="panel-body">
				<div class="form-row">
					<div class="form-group col-xxl-1 col-lg-1 col-md-1 col-sm-4">
						<input type="number" class="form-control" id="id" name="id" value="{{Request::get('id')}}" placeholder="ID"/>
					</div>
					<div class="form-group col-xxl-2 col-lg-3 col-md-3 col-sm-4">
						<input type="text" class="form-control" id="username" name="username" value="{{Request::get('username')}}" placeholder="用户名"/>
					</div>
					<div class="form-group col-xxl-2 col-lg-3 col-md-3 col-sm-4">
						<input type="text" class="form-control" id="wechat" name="wechat" value="{{Request::get('wechat')}}" placeholder="微信"/>
					</div>
					<div class="form-group col-xxl-2 col-lg-3 col-md-3 col-sm-4">
						<input type="number" class="form-control" id="qq" name="qq" value="{{Request::get('qq')}}" placeholder="QQ"/>
					</div>
					<div class="form-group col-xxl-1 col-lg-2 col-md-2 col-sm-4">
						<input type="number" class="form-control" id="port" name="port" value="{{Request::get('port')}}" placeholder="端口"/>
					</div>
					<div class="form-group col-xxl-1 col-lg-3 col-md-3 col-sm-4">
						<select class="form-control" id="pay_way" name="pay_way" onChange="Search()">
							<option value="" @if(Request::get('pay_way') == '') selected hidden @endif>付费方式</option>
							<option value="0" @if(Request::get('pay_way') == '0') selected hidden @endif>免费</option>
							<option value="1" @if(Request::get('pay_way') == '1') selected hidden @endif>月付</option>
							<option value="2" @if(Request::get('pay_way') == '2') selected hidden @endif>季付</option>
							<option value="3" @if(Request::get('pay_way') == '3') selected hidden @endif>半年付</option>
							<option value="4" @if(Request::get('pay_way') == '4') selected hidden @endif>年付</option>
						</select>
					</div>
					<div class="form-group col-xxl-1 col-lg-3 col-md-3 col-4">
						<select class="form-control" id="status" name="status" onChange="Search()">
							<option value="" @if(Request::get('status') == '') selected hidden @endif>账号状态</option>
							<option value="-1" @if(Request::get('status') == '-1') selected hidden @endif>禁用</option>
							<option value="0" @if(Request::get('status') == '0') selected hidden @endif>未激活</option>
							<option value="1" @if(Request::get('status') == '1') selected hidden @endif>正常</option>
						</select>
					</div>
					<div class="form-group col-xxl-1 col-lg-3 col-md-3 col-4">
						<select class="form-control" id="enable" name="enable" onChange="Search()">
							<option value="" @if(Request::get('enable') == '') selected hidden @endif>代理状态</option>
							<option value="1" @if(Request::get('enable') == '1') selected hidden @endif>启用</option>
							<option value="0" @if(Request::get('enable') == '0') selected hidden @endif>禁用</option>
						</select>
					</div>
					<div class="form-group col-xxl-1 col-lg-3 col-md-3 col-4 btn-group">
						<button class="btn btn-primary" onclick="Search()">搜索</button>
						<a href="/admin/userList" class="btn btn-danger">重置</a>
					</div>
				</div>
				<table class="text-md-center" data-toggle="table" data-mobile-responsive="true">
					<thead class="thead-default">
					<tr>
						<th> #</th>
						<th> 用户名</th>
						<th> 余额</th>
						<th> 端口</th>
						<th> 订阅码</th>
						<th> 流量使用</th>
						<th> 最后使用</th>
						<th> 有效期</th>
						<th> 状态</th>
						<th> 代理</th>
						<th> 操作</th>
					</tr>
					</thead>
					<tbody>
					@if ($userList->isEmpty())
						<tr>
							<td colspan="11">暂无数据</td>
						</tr>
					@else
						@foreach ($userList as $user)
							<tr class="{{$user->trafficWarning ? ' table-danger' : ''}}">
								<td> {{$user->id}} </td>
								<td> {{$user->username}} </td>
								<td> {{$user->balance}} </td>
								<td>
									{!!$user->port? : '<span class="badge badge-lg badge-danger"> 未分配 </span>'!!}
								</td>
								<td>
									<a href="javascript:" class="copySubscribeLink" data-clipboard-action="copy" data-clipboard-text="{{$user->link}}">{{$user->subscribe->code}}</a>
								</td>
								<td> {{$user->used_flow}} / {{$user->transfer_enable}} </td>
								<td> {{$user->t? date('Y-m-d H:i', $user->t): '未使用'}} </td>

								<td>
									@if ($user->expireWarning == '-1')
										<span class="badge badge-lg badge-danger"> {{$user->expire_time}} </span>
									@elseif ($user->expireWarning == '0')
										<span class="badge badge-lg badge-warning"> {{$user->expire_time}} </span>
									@elseif ($user->expireWarning == '1')
										<span class="badge badge-lg badge-default"> {{$user->expire_time}} </span>
									@else
										{{$user->expire_time}}
									@endif
								</td>
								<td>
									@if ($user->status > 0)
										<span class="badge badge-lg badge-primary"><i class="wb-check" aria-hidden="true"></i></span>
									@elseif ($user->status < 0)
										<span class="badge badge-lg badge-danger"><i class="wb-close" aria-hidden="true"></i></span>
									@else
										<span class="badge badge-lg badge-default"><i class="wb-minus" aria-hidden="true"></i></span>
									@endif
								</td>
								<td>
									<span class="badge badge-lg badge-{{$user->enable?'info':'danger'}}"><i class="wb-{{$user->enable?'check':'close'}}" aria-hidden="true"></i></span>
								</td>
								<td>
									<div class="btn-group">
										<a href="/admin/editUser/{{$user->id}}{{Request::getQueryString()? '?'.Request::getQueryString() : ''}}" class="btn btn-primary"><i class="icon wb-edit" aria-hidden="true"></i></a>
										<a href="javascript:delUser('{{$user->id}}','{{$user->username}}');" class="btn btn-danger"><i class="icon wb-trash" aria-hidden="true"></i></a>
										<a href="/admin/export/{{$user->id}}" class="btn btn-primary"><i class="icon wb-code" aria-hidden="true"></i></a>
										<a href="/admin/userMonitor/{{$user->id}}" class="btn btn-primary"><i class="icon wb-stats-bars" aria-hidden="true"></i></a>
										<a href="/admin/onlineIPMonitor?id={{$user->id}}" class="btn btn-primary"><i class="icon wb-cloud" aria-hidden="true"></i></a>
										<a href="javascript:resetTraffic('{{$user->id}}','{{$user->username}}');" class="btn btn-primary"><i class="icon wb-reload" aria-hidden="true"></i></a>
										<a href="javascript:switchToUser('{{$user->id}}');" class="btn btn-primary"><i class="icon wb-user" aria-hidden="true"></i></a>
									</div>
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-sm-4">
						共 <code>{{$userList->total()}}</code> 个账号
					</div>
					<div class="col-sm-8">
						<nav class="Page navigation float-right">
							{{$userList->links()}}
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script src="/assets/global/vendor/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="/assets/global/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.min.js" type="text/javascript"></script>
	<script src="/assets/custom/Plugin/clipboardjs/clipboard.min.js" type="text/javascript"></script>
	<script type="text/javascript">
        // 导出原版json配置
        function exportSSJson() {
            swal.fire({
                title: '导出成功',
                text: '成功导出原版SS的用户配置信息，加密方式为系统默认的加密方式',
                type: 'success',
                timer: 1300,
                showConfirmButton: false,
            }).then(() => window.location.href = '/admin/exportSSJson')
        }

        // 批量生成账号
        function batchAddUsers() {
            swal.fire({
                title: '注意',
                text: '将自动生成5个账号，确定继续吗？',
                type: 'question',
                showCancelButton: true,
                cancelButtonText: '{{trans('home.ticket_close')}}',
                confirmButtonText: '{{trans('home.ticket_confirm')}}',
            }).then((result) => {
                if (result.value) {
                    $.post("/admin/batchAddUsers", {_token: '{{csrf_token()}}'}, function (ret) {
                        if (ret.status === 'success') {
                            swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false})
                                .then(() => window.location.reload())
                        } else {
                            swal.fire({title: ret.message, type: "error"}).then(() => window.location.reload())
                        }
                    });
                }
            });
        }

        //回车检测
        $(document).on("keypress", "input", function (e) {
            if (e.which === 13) {
                Search();
                return false;
            }
        });

        // 搜索
        function Search() {
            window.location.href = '/admin/userList' + '?id=' + $("#id").val() + '&username=' + $("#username").val() + '&wechat=' + $("#wechat").val() + '&qq=' + $("#qq").val() + '&port=' + $("#port").val() + '&pay_way=' + $("#pay_way option:selected").val() + '&status=' + $("#status option:selected").val() + '&enable=' + $("#enable option:selected").val();
        }

        // 删除账号
        function delUser(id, username) {
            swal.fire({
                title: '警告',
                text: '确定删除用户 【' + username + '】 ？',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '{{trans('home.ticket_close')}}',
                confirmButtonText: '{{trans('home.ticket_confirm')}}',
            }).then((result) => {
                if (result.value) {
                    $.post("/admin/delUser", {id: id, _token: '{{csrf_token()}}'}, function (ret) {
                        if (ret.status === 'success') {
                            swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false})
                                .then(() => window.location.reload())
                        } else {
                            swal.fire({title: ret.message, type: "error"}).then(() => window.location.reload())
                        }
                    });
                }
            });
        }

        // 重置流量
        function resetTraffic(id, username) {
            swal.fire({
                title: '警告',
                text: '确定重置 【' + username + '】 流量吗？',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: '{{trans('home.ticket_close')}}',
                confirmButtonText: '{{trans('home.ticket_confirm')}}',
            }).then((result) => {
                if (result.value) {
                    $.post("/admin/resetUserTraffic", {_token: '{{csrf_token()}}', id: id}, function (ret) {
                        if (ret.status === 'success') {
                            swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false})
                                .then(() => window.location.reload())
                        } else {
                            swal.fire({title: ret.message, type: "error"}).then(() => window.location.reload())
                        }
                    });
                }
            });
        }

        // 切换用户身份
        function switchToUser(id) {
            $.ajax({
                'url': "/admin/switchToUser",
                'data': {
                    'user_id': id,
                    '_token': '{{csrf_token()}}'
                },
                'dataType': "json",
                'type': "POST",
                success: function (ret) {
                    if (ret.status === 'success') {
                        swal.fire({title: ret.message, type: 'success', timer: 1000, showConfirmButton: false})
                            .then(() => window.location.href = "/")
                    } else {
                        swal.fire({title: ret.message, type: "error"}).then(() => window.location.reload())
                    }
                }
            });
        }

        const clipboard = new ClipboardJS('.copySubscribeLink');
        clipboard.on('success', function () {
            swal.fire({
                title: '复制成功',
                type: 'success',
                timer: 1000,
                showConfirmButton: false
            });
        });
        clipboard.on('error', function () {
            swal.fire({
                title: '复制失败，请手动复制',
                type: 'error',
                timer: 1500,
                showConfirmButton: false
            });
        });
	</script>
@endsection
