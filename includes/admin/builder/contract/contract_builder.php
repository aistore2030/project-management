<?php
add_action( 'admin_menu' , 'register_pto_contract_builder_page', 29 ); 
function register_pto_contract_builder_page() {
	$mypage = add_submenu_page(	
				'pto-dashboard',
				__('Contract Template', 'cqpim'),		
				__('Contract Template', 'cqpim'), 			
				'edit_cqpim_contract_template', 			
				'pto-contract-template', 		
				'pto_contract_template'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_contract_template() { ?>
	<div class="cqpim-dash-item-full tasks-box">
		<br />
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php _e('Contract Template', 'cqpim'); ?> </span>
				</div>
			</div>
			%%PROJECT_TITLE%%
			%%CLIENT_NAME%%
			%%CLIENT_COMPANY_NAME%%
			%%CLIENT_ADDRESS%%
			%%CLIENT_EMAIL_ADDRESS%%
			%%CLIENT_POSTCODE%%
			%%CLIENT_PHONE%%
			%%PROJECT_BRIEF%%
			%%START_DATE%%
			%%FINISH_DATE%%
			%%MILESTONE_TABLE%%
			%%COST_BREAKDOWN_TABLE%%
			%%DEPOSIT_PERCENTAGE%%
			%%TERMS%%
			%%ACCEPTANCE_TEXT%%
			%%YOUR_COMPANY_NAME%%
			%%YOUR_COMPANY_ADDRESS%%
			%%YOUR_COMPANY_POSTCODE%%
			%%YOUR_COMPANY_SALES_EMAIL%%
			%%YOUR_COMPANY_PHONE%%
		</div>
	</div>
<?php }