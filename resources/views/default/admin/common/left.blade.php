<div class="leftpanel">

	<div class="logopanel">
		<h1><span>[</span> 用户中心 <span>]</span></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<!-- This is only visible to small devices -->
		<div class="visible-xs hidden-sm hidden-md hidden-lg">
			<div class="media userlogged">
				<img alt="" src="{{ asset('/common/images/photos/loggeduser.png')}}" class="media-object">
				<div class="media-body">
					<h4>{{Admin::user()->name}}</h4>
					<span>早上好</span>
				</div>
			</div>

			<h5 class="sidebartitle actitle">账户中心</h5>
			<ul class="nav nav-pills nav-stacked nav-bracket mb30">
				<li><a href="profile.html"><i class="fa fa-user"></i> <span>我的资料</span></a></li>
				<li><a href=""><i class="fa fa-cog"></i> <span>账户设置</span></a></li>
				<li><a href=""><i class="fa fa-question-circle"></i> <span>帮助中心</span></a></li>
				<li><a href="{{ url('/admin_login/logout') }}"><i class="fa fa-sign-out"></i> <span>登出</span></a></li>
			</ul>
		</div>

		<h5 class="sidebartitle">导航</h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">
			<li class="active"><a href="index.html"><i class="fa fa-home"></i> <span>控制面板</span></a></li>
			<li class="nav-parent"><a href=""><i class="fa fa-edit"></i> <span>资金流动</span></a>
				<ul class="children">
					<li><a href="general-forms.html"><i class="fa fa-caret-right"></i> 充值</a></li>
					<li><a href="general-forms.html"><i class="fa fa-caret-right"></i> 提现</a></li>
				</ul>
			</li>
			<li class="nav-parent"><a href=""><i class="fa fa-suitcase"></i> <span>借款项目</span></a>
				<ul class="children">
					<li><a href="graphs.html"><i class="fa fa-caret-right"></i> 申请借款</a></li>
				</ul>
			</li>
			<li class="nav-parent"><a href=""><i class="fa fa-bug"></i> <span>理财项目</span></a>
				<ul class="children">
					<li><a href="bug-tracker.html"><i class="fa fa-caret-right"></i> 提现</a></li>
					<li><a href="bug-issues.html"><i class="fa fa-caret-right"></i> 充值</a></li>
				</ul>
			</li>
			<li class="nav-parent"><a href=""><i class="fa fa-bug"></i> <span>会员管理</span></a>
				<ul class="children">
					<li><a href="bug-tracker.html"><i class="fa fa-caret-right"></i> 提现</a></li>
					<li><a href="bug-issues.html"><i class="fa fa-caret-right"></i> 充值</a></li>
				</ul>
			</li>
			<li class="nav-parent"><a href=""><i class="fa fa-bug"></i> <span>职员管理</span></a>
				<ul class="children">
					<li><a href="bug-tracker.html"><i class="fa fa-caret-right"></i> 提现</a></li>
				</ul>
			</li>
			<li class="nav-parent"><a href=""><i class="fa fa-bug"></i> <span>权限管理</span></a>
				<ul class="children">
					<li><a href="bug-tracker.html"><i class="fa fa-caret-right"></i> 提现</a></li>
				</ul>
			</li>
		</ul>

		<div class="infosummary">
			<h5 class="sidebartitle">平台信息</h5>
			<ul>
				<li>
					<div class="datainfo">
						<span class="text-muted">总用户数</span>
						<h4>630, 22</h4>
					</div>
					<div id="sidebar-chart" class="chart"></div>
				</li>
				<li>
					<div class="datainfo">
						<span class="text-muted">累计借出金额</span>
						<h4>1, 332, 81</h4>
					</div>
					<div id="sidebar-chart2" class="chart"></div>
				</li>
				<li>
					<div class="datainfo">
						<span class="text-muted">累计为用户赚取</span>
						<h4>82, 777, 00</h4>
					</div>
					<div id="sidebar-chart3" class="chart"></div>
				</li>
			</ul>
		</div><!-- infosummary -->

	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->