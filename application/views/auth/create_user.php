<div class="container">
	<!--<div class="container-fluid">-->
	<div class="row-fluid">
		<div class="span8 offset2 pagination-centered">
			<div class="well">
				<h2><?php echo lang('create_user_heading'); ?></h2>
				<p><?php echo lang('create_user_subheading'); ?></p>

				<div id="infoMessage"><?php echo $message; ?></div>

				<?php echo form_open("auth/create_user"); ?>

				<p>
					<?php echo lang('create_user_fname_label', 'first_name'); ?> <br />
					<?php echo form_input($first_name); ?>
				</p>

				<p>
					<?php echo lang('create_user_lname_label', 'first_name'); ?> <br />
					<?php echo form_input($last_name); ?>
				</p>

				<p>
					<?php echo lang('create_user_company_label', 'company'); ?> <br />
					<?php echo form_input($company); ?>
				</p>

				<p>
					<?php echo lang('create_user_email_label', 'email'); ?> <br />
					<?php echo form_input($email); ?>
				</p>

				<p>
					<?php echo lang('create_user_phone_label', 'phone'); ?> <br />
					<?php echo form_input($phone); ?>
				</p>

				<p>
					<?php echo lang('create_user_password_label', 'password'); ?> <br />
					<?php echo form_input($password); ?>
				</p>

				<p>
					<?php echo lang('create_user_password_confirm_label', 'password_confirm'); ?> <br />
					<?php echo form_input($password_confirm); ?>
				</p>


				<p><?php echo form_submit('submit', lang('create_user_submit_btn')); ?></p>
				<p><button type="submit" name="submit" class="btn btn-primary"><i class="icon-user icon-white"></i>  Create User</button><?php echo nbs(6); ?><button type="reset" class="btn"><i class="icon-remove-sign"></i> Clear</button><?php echo nbs(6); ?><a href="<?php echo base_url() . "auth/users"; ?>" class="btn" ><i class="icon-step-backward"></i> Go back</a></p>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>