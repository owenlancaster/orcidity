<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
        <div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<!--<a class="brand" href="<?php echo base_url(); ?>"><img src="<?php echo base_url() . "resources/img/" . $this->config->item('logo');?>"></a>-->
            <a class="brand" href="#" >ORCIDity</a>
<!--			<p class="navbar-text pull-right">
				<?php if (!$this->ion_auth->logged_in()): ?>
					<a href="<?php echo base_url() . "auth/login";?>" class="navbar-link">Login</a> | <a href="<?php echo base_url() . "auth/signup";?>" class="navbar-link">Sign up</a>
				<?php else: ?>
					<?php $user_id = $this->session->userdata( 'user_id' ); ?>
					<?php if ( $this->ion_auth->is_admin()): ?>
					<a href="<?php echo base_url(); ?>admin" class="navbar-link">Admin</a> | 
				<?php endif; ?>
					<a href="<?php echo base_url() . "auth/user_profile/" . $user_id;?>" class="navbar-link">Profile</a> | <a href="<?php echo base_url() . "auth/logout";?>" class="navbar-link">Logout</a>
				<?php endif; ?>
            </p>-->

			<ul class="nav">  
				<li class="<?php echo isActive($pageName,"home")?>"><a href="<?php echo  base_url()?>">Home</a></li>
<!--				<li class="dropdown">  
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">About&nbsp;<b class="caret"></b></a>  
					<ul class="dropdown-menu">  
						<li><a href="<?php echo base_url(); ?>about/orcidity">ORCIDity</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo base_url(); ?>about/contact">Contact</a></li>
					</ul>  
				</li>  -->
			</ul>
		</div>
	</div>
</div>	
