<div class="headerbar">
<a class="menutoggle"><i class="fa fa-bars"></i></a>

<form class="searchform" action="/" method="post">
	<input type="text" class="form-control" name="keyword" placeholder="输入搜索关键词" />
</form>

<div class="header-right">
	<ul class="headermenu">
		<li>
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
					<i class="glyphicon glyphicon-user"></i>
					<span class="badge">2</span>
				</button>
				<div class="dropdown-menu dropdown-menu-head pull-right">
					<h5 class="title">2 Newly Registered Users</h5>
					<ul class="dropdown-list user-list">
						<li class="new">
							<div class="thumb"><a href=""><img src="{{ asset('/common/images/photos/user1.png')}}" alt="" /></a></div>
							<div class="desc">
								<h5><a href="">Draniem Daamul (@draniem)</a> <span class="badge badge-success">new</span></h5>
							</div>
						</li>
						<li class="new"><a href="">See All Users</a></li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
					<i class="glyphicon glyphicon-envelope"></i>
					<span class="badge">1</span>
				</button>
				<div class="dropdown-menu dropdown-menu-head pull-right">
					<h5 class="title">You Have 1 New Message</h5>
					<ul class="dropdown-list gen-list">
						<li class="new">
							<a href="">
								<span class="thumb"><img src="{{ asset('/common/images/photos/user1.png')}}" alt="" /></span>
                    <span class="desc">
                      <span class="name">Draniem Daamul <span class="badge badge-success">new</span></span>
                      <span class="msg">Lorem ipsum dolor sit amet...</span>
                    </span>
							</a>
						</li>
						<li class="new"><a href="">Read All Messages</a></li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			<div class="btn-group">
				<button class="btn btn-default dropdown-toggle tp-icon" data-toggle="dropdown">
					<i class="glyphicon glyphicon-globe"></i>
					<span class="badge">5</span>
				</button>
				<div class="dropdown-menu dropdown-menu-head pull-right">
					<h5 class="title">You Have 5 New Notifications</h5>
					<ul class="dropdown-list gen-list">
						<li class="new">
							<a href="">
								<span class="thumb"><img src="{{ asset('/common/images/photos/user4.png')}}" alt="" /></span>
                    <span class="desc">
                      <span class="name">Zaham Sindilmaca <span class="badge badge-success">new</span></span>
                      <span class="msg">is now following you</span>
                    </span>
							</a>
						</li>
						<li class="new"><a href="">See All Notifications</a></li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			<div class="btn-group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<img src="{{ asset('/common/images/photos/loggeduser.png')}}" alt="" />
					{{Admin::user()->name}}
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-usermenu pull-right">
					<li><a href="profile.html"><i class="glyphicon glyphicon-user"></i> 我的资料</a></li>
					<li><a href="#"><i class="glyphicon glyphicon-cog"></i> 账户设置</a></li>
					<li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> 帮助中心</a></li>
					<li><a href="{{ url('/admin_login/logout') }}"><i class="glyphicon glyphicon-log-out"></i> 登出</a></li>
				</ul>
			</div>
		</li>
		<li>
			<button id="chatview" class="btn btn-default tp-icon chat-icon">
				<i class="glyphicon glyphicon-comment"></i>
			</button>
		</li>
	</ul>
</div><!-- header-right -->
</div><!-- headerbar -->