<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Dompet Laravel'),


    'public' => [
        'favicon' => 'media/img/logo/favicon.ico',
        'fonts' => [
            'google' => [
                'families' => [
                    'Poppins:300,400,500,600,700',
                ]
            ]
        ],
		'global' => [
			'css' => [
				'vendor/jquery-nice-select/css/nice-select.css',
				'css/style.css',
			],
			'js' => [
				'top'=>[
					'vendor/global/global.min.js',
					'vendor/jquery-nice-select/js/jquery.nice-select.min.js',	
				],
				'bottom'=>[
					'js/custom.min.js',
					'js/dlabnav-init.js',
				],
			],
		],
		'pagelevel' => [
			'css' => [
				'DompetadminController_dashboard' => [
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_dashboard_2' => [
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_my_wallet' => [
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_page_invoices' => [
							'vendor/datatables/css/jquery.dataTables.min.css',
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_cards_center' => [
							'vendor/owl-carousel/owl.carousel.css',
				],
				'DompetadminController_page_transaction' => [
							'vendor/datatables/css/jquery.dataTables.min.css',
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_transaction_details' => [
							'vendor/datatables/css/jquery.dataTables.min.css',
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_app_profile' => [
							'vendor/lightgallery/css/lightgallery.min.css',
				],
				'DompetadminController_post_details' => [
							'vendor/lightgallery/css/lightgallery.min.css',
				],
				'DompetadminController_app_calender' => [
							'vendor/fullcalendar/css/main.min.css',
				],
				'DompetadminController_chart_chartist' => [
							'vendor/chartist/css/chartist.min.css',
				],
				'DompetadminController_chart_chartjs' => [
				],
				'DompetadminController_chart_flot' => [
				],
				'DompetadminController_chart_morris' => [
				],
				'DompetadminController_chart_peity' => [
				],
				'DompetadminController_chart_sparkline' => [
				],
				'DompetadminController_ecom_checkout' => [
				],
				'DompetadminController_ecom_customers' => [
				],
				'DompetadminController_ecom_invoice' => [
					'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],
				'DompetadminController_ecom_product_detail' => [
							'vendor/star-rating/star-rating-svg.css',
				],
				'DompetadminController_ecom_product_grid' => [
				],
				'DompetadminController_ecom_product_list' => [
							'vendor/star-rating/star-rating-svg.css',
				],
				'DompetadminController_ecom_product_order' => [
				],
				'DompetadminController_email_compose' => [
							'vendor/dropzone/dist/dropzone.css',
				],
				'DompetadminController_email_inbox' => [
				],
				'DompetadminController_email_read' => [
				],
				'DompetadminController_form_ckeditor' => [
				],
				'DompetadminController_form_element' => [
				],
				'DompetadminController_form_pickers' => [
							'vendor/bootstrap-daterangepicker/daterangepicker.css',
							'vendor/clockpicker/css/bootstrap-clockpicker.min.css',
							'vendor/jquery-asColorPicker/css/asColorPicker.min.css',
							'vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
							'vendor/pickadate/themes/default.css',
							'vendor/pickadate/themes/default.date.css',
							'https://fonts.googleapis.com/icon?family=Material+Icons',
				],
				'DompetadminController_form_validation' => [
				],
				'DompetadminController_form_wizard' => [
							'vendor/jquery-smartwizard/dist/css/smart_wizard.min.css',
				],
				'DompetadminController_map_jqvmap' => [
							'vendor/jqvmap/css/jqvmap.min.css',
				],
				'DompetadminController_login' => [
							'vendor/sweetalert2/dist/sweetalert2.min.css',
				],
				'DompetadminController_table_bootstrap_basic' => [
				],
				'DompetadminController_table_datatable_basic' => [
							'vendor/datatables/css/jquery.dataTables.min.css',
				],
				'DompetadminController_uc_lightgallery' => [
							'vendor/lightgallery/css/lightgallery.min.css',
				],
				'DompetadminController_uc_nestable' => [
							'vendor/nestable2/css/jquery.nestable.min.css',
				],
				'DompetadminController_uc_noui_slider' => [
							'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_uc_select2' => [
							'vendor/select2/css/select2.min.css',
				],
				'DompetadminController_uc_sweetalert' => [
							'vendor/sweetalert2/dist/sweetalert2.min.css',
				],
				'DompetadminController_uc_toastr' => [
							'vendor/toastr/css/toastr.min.css',
				],
				'DompetadminController_ui_accordion' => [
				],
				'DompetadminController_ui_alert' => [
				],
				'DompetadminController_ui_badge' => [
				],
				'DompetadminController_ui_button' => [
				],
				'DompetadminController_ui_button_group' => [
				],
				'DompetadminController_ui_card' => [
				],
				'DompetadminController_ui_carousel' => [
				],
				'DompetadminController_ui_dropdown' => [
				],
				'DompetadminController_ui_grid' => [
				],
				'DompetadminController_ui_list_group' => [
				],
				'DompetadminController_ui_modal' => [
				],
				'DompetadminController_ui_pagination' => [
				],
				'DompetadminController_ui_popover' => [
				],
				'DompetadminController_ui_progressbar' => [
				],
				'DompetadminController_ui_tab' => [
				],
				'DompetadminController_ui_typography' => [
				],
				'DompetadminController_widget_basic' => [
							'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
							'vendor/chartist/css/chartist.min.css',
				],
				'DompetadminController_page_error_400' => [
					'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],
				'DompetadminController_demo_modules_index' => [
				],
				'DompetadminController_demo_modules_add' => [
				],


				'DompetadminController_add_email' => [
					'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
					'vendor/select2/css/select2.min.css',
				],
				'DompetadminController_add_blog' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/select2/css/select2.min.css',

				],
				'DompetadminController_blog' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css',
				],
				'DompetadminController_blog_category' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_content' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css'
				],
				'DompetadminController_content_add' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],
				'DompetadminController_email_template' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_index_3' => [
				    'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_index_4' => [
				    'vendor/jquery-nice-select/css/nice-select.css',
				    'vendor/nouislider/nouislider.min.css',
				    'vendor/swiper/css/swiper-bundle.min.css',
				],
				'DompetadminController_index_5' => [
				    'vendor/nouislider/nouislider.min.css',
				    'vendor/swiper/css/swiper-bundle.min.css',
				],
				'DompetadminController_index_6' => [
				    
				],
				'DompetadminController_index_7' => [
				    'vendor/nouislider/nouislider.min.css',
				    'vendor/swiper/css/swiper-bundle.min.css',
				],
				'DompetadminController_index_8' => [
				    'vendor/nouislider/nouislider.min.css',
				    'vendor/swiper/css/swiper-bundle.min.css',
				],
				'DompetadminController_menu' => [
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				    'vendor/nestable2/css/jquery.nestable.min.css',
				    'vendor/nouislider/nouislider.min.css',
				],
				'DompetadminController_widget_card' => [
				    'vendor/chartist/css/chartist.min.css',
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],
				'DompetadminController_widget_chart' => [
				    'vendor/chartist/css/chartist.min.css',
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],
				'DompetadminController_widget_list' => [
				    'vendor/chartist/css/chartist.min.css',
				    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
				],


			],
			'js' => [
				'DompetadminController_dashboard' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/nouislider/nouislider.min.js',
							'vendor/wnumb/wNumb.js',
							'js/dashboard/dashboard-1.js',
				],
				'DompetadminController_dashboard_2' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/nouislider/nouislider.min.js',
							'vendor/wnumb/wNumb.js',
							'js/dashboard/dashboard-1.js',
				],
				 'DompetadminController_my_wallet' => [
				 			'vendor/chart-js/chart.bundle.min.js',
				 			'vendor/apexchart/apexchart.js',
				 			'vendor/nouislider/nouislider.min.js',
				 			'vendor/wnumb/wNumb.js',
				 			'js/dashboard/my-wallet.js',
				],
				'DompetadminController_page_invoices' => [
					'vendor/chart-js/chart.bundle.min.js',
					'vendor/datatables/js/jquery.dataTables.min.js',
					'js/plugins-init/datatables.init.js',
				],
				'DompetadminController_cards_center' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/owl-carousel/owl.carousel.js',
							'js/dashboard/cards-center.js',
				],
				'DompetadminController_page_transaction' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/datatables/js/jquery.dataTables.min.js',
							'js/plugins-init/datatables.init.js',
				],
				'DompetadminController_transaction_details' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'js/dashboard/transaction-details.js',
							'vendor/datatables/js/jquery.dataTables.min.js',
							'js/plugins-init/datatables.init.js',
				],
				'DompetadminController_app_calender' => [
							'vendor/moment/moment.min.js',
							'vendor/fullcalendar/js/main.min.js',
							'js/plugins-init/fullcalendar-init.js',
				],
				'DompetadminController_app_profile' => [
							'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/lightgallery/js/lightgallery-all.min.js',
				],
				'DompetadminController_post_details' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/lightgallery/js/lightgallery-all.min.js',
				],
				'DompetadminController_chart_chartist' => [
						    'vendor/chart-js/chart.bundle.min.js',
						    'vendor/apexchart/apexchart.js',
							'vendor/chartist/js/chartist.min.js',
							'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
							'js/plugins-init/chartist-init.js',
				],
				'DompetadminController_chart_chartjs' => [
						    'vendor/chart-js/chart.bundle.min.js',
						    'vendor/apexchart/apexchart.js',
							'js/plugins-init/chartjs-init.js',
				],
				'DompetadminController_chart_flot' => [
						    'vendor/chart-js/chart.bundle.min.js',
						    'vendor/apexchart/apexchart.js',
							'vendor/flot/jquery.flot.js',
							'vendor/flot/jquery.flot.pie.js',
							'vendor/flot/jquery.flot.resize.js',
							'vendor/flot-spline/jquery.flot.spline.min.js',
							'js/plugins-init/flot-init.js',
				],
				'DompetadminController_chart_morris' => [
						    'vendor/chart-js/chart.bundle.min.js',
						    'vendor/apexchart/apexchart.js',
							'vendor/raphael/raphael.min.js',
							'vendor/morris/morris.min.js',
							'js/plugins-init/morris-init.js',
				],
				'DompetadminController_chart_peity' => [
						    'vendor/chart-js/chart.bundle.min.js',
							'vendor/peity/jquery.peity.min.js',
							'js/plugins-init/piety-init.js',
				],
				'DompetadminController_chart_sparkline' => [
						    'vendor/chart-js/chart.bundle.min.js',
						    'vendor/apexchart/apexchart.js',
							'vendor/jquery-sparkline/jquery.sparkline.min.js',
							'js/plugins-init/sparkline-init.js',
							'vendor/svganimation/vivus.min.js',
							'vendor/svganimation/svg.animation.js',
				],
				'DompetadminController_ecom_checkout' => [
				],
				'DompetadminController_ecom_customers' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/highlightjs/highlight.pack.min.js',
				],
				'DompetadminController_ecom_invoice' => [
				],
				'DompetadminController_ecom_product_detail' => [
							'vendor/star-rating/jquery.star-rating-svg.js',
                ],
				'DompetadminController_ecom_product_grid' => [
				],
				'DompetadminController_ecom_product_list' => [
							'vendor/star-rating/jquery.star-rating-svg.js',
				],
				'DompetadminController_ecom_product_order' => [
				],
				'DompetadminController_email_compose' => [
							'vendor/dropzone/dist/dropzone.js',
				],
				'DompetadminController_email_inbox' => [
				],
				'DompetadminController_email_read' => [
				],
				'DompetadminController_form_ckeditor' => [
							'vendor/ckeditor/ckeditor.js',
				],
				'DompetadminController_form_element' => [
				],
				'DompetadminController_form_pickers' => [
							'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/moment/moment.min.js',
							'vendor/bootstrap-daterangepicker/daterangepicker.js',
							'vendor/clockpicker/js/bootstrap-clockpicker.min.js',
							'vendor/jquery-asColor/jquery-asColor.min.js',
							'vendor/jquery-asGradient/jquery-asGradient.min.js',
							'vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js',
							'vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
							'vendor/pickadate/picker.js',
							'vendor/pickadate/picker.time.js',
							'vendor/pickadate/picker.date.js',
							'js/plugins-init/bs-daterange-picker-init.js',
							'js/plugins-init/clock-picker-init.js',
							'js/plugins-init/jquery-asColorPicker.init.js',
							'js/plugins-init/material-date-picker-init.js',
							'js/plugins-init/pickadate-init.js',
				],
				'DompetadminController_form_validation' => [
				],
				'DompetadminController_form_wizard' => [
							'vendor/jquery-steps/build/jquery.steps.min.js',
							'vendor/jquery-validation/jquery.validate.min.js',
							'js/plugins-init/jquery.validate-init.js',
							'vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js',
				],
				'DompetadminController_map_jqvmap' => [
							'vendor/jqvmap/js/jquery.vmap.min.js',
							'vendor/jqvmap/js/jquery.vmap.world.js',
							'vendor/jqvmap/js/jquery.vmap.usa.js',
							'js/plugins-init/jqvmap-init.js',
				],
				'DompetadminController_page_error_400' => [
				],
				'DompetadminController_page_error_403' => [
				],
				'DompetadminController_page_error_404' => [
				],
				'DompetadminController_page_error_500' => [
				],
				'DompetadminController_page_error_503' => [
				],
				'DompetadminController_page_forgot_password' => [
				],
				'DompetadminController_page_lock_screen' => [
							'vendor/dlabnav/dlabnav.min.js',
				],
				'DompetadminController_page_login' => [
				],
				'DompetadminController_page_register' => [
				],
				'DompetadminController_table_bootstrap_basic' => [
				],
				'DompetadminController_table_datatable_basic' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/datatables/js/jquery.dataTables.min.js',
							'js/plugins-init/datatables.init.js',
				],
				'DompetadminController_uc_lightgallery' => [
							'vendor/lightgallery/js/lightgallery-all.min.js',
				],
				'DompetadminController_uc_nestable' => [
							'vendor/nestable2/js/jquery.nestable.min.js',
							'js/plugins-init/nestable-init.js',
				],
				'DompetadminController_uc_noui_slider' => [
							'vendor/nouislider/nouislider.min.js',
							'vendor/wnumb/wNumb.js',
							'js/plugins-init/nouislider-init.js',
				],
				'DompetadminController_uc_select2' => [
							'vendor/select2/js/select2.full.min.js',
							'js/plugins-init/select2-init.js',
				],
				'DompetadminController_uc_sweetalert' => [
							'vendor/sweetalert2/dist/sweetalert2.min.js',
							'js/plugins-init/sweetalert.init.js',
				],
				'DompetadminController_uc_toastr' => [
							'vendor/toastr/js/toastr.min.js',
							'js/plugins-init/toastr-init.js',
				],
				'DompetadminController_ui_accordion' => [
				],
				'DompetadminController_ui_alert' => [
				],
				'DompetadminController_ui_badge' => [
				],
				'DompetadminController_ui_button' => [
				],
				'DompetadminController_ui_button_group' => [
				],
				'DompetadminController_ui_card' => [
				],
				'DompetadminController_ui_carousel' => [
				],
				'DompetadminController_ui_dropdown' => [
				],
				'DompetadminController_ui_grid' => [
				],
				'DompetadminController_ui_list_group' => [
				],
				'DompetadminController_ui_modal' => [
				],
				'DompetadminController_ui_pagination' => [
				],
				'DompetadminController_ui_popover' => [
				],
				'DompetadminController_ui_progressbar' => [
				],
				'DompetadminController_ui_tab' => [
				],
				'DompetadminController_ui_typography' => [
				],
				'DompetadminController_widget_basic' => [
							'vendor/chart-js/chart.bundle.min.js',
							'vendor/apexchart/apexchart.js',
							'vendor/chartist/js/chartist.min.js',
							'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
							'vendor/flot/jquery.flot.js',
							'vendor/flot/jquery.flot.pie.js',
							'vendor/flot/jquery.flot.resize.js',
							'vendor/flot-spline/jquery.flot.spline.min.js',
							'vendor/jquery-sparkline/jquery.sparkline.min.js',
							'js/plugins-init/sparkline-init.js',
							'vendor/peity/jquery.peity.min.js',
							'js/plugins-init/piety-init.js',
							'js/plugins-init/widgets-script-init.js',
				],
				'DompetadminController_demo_modules_add' => [
				],




				'DompetadminController_add_email' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				    'js/dashboard/cms.js',
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/ckeditor/ckeditor.js',
				    'vendor/select2/js/select2.full.min.js',
				    'js/plugins-init/select2-init.js',
				    'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_add_blog' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				    'js/dashboard/cms.js',
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/ckeditor/ckeditor.js',
				    'vendor/select2/js/select2.full.min.js',
				    'js/plugins-init/select2-init.js',
				    'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_blog' => [
			    	'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
			    	'js/dashboard/cms.js',
			    	'vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js'
				],
				'DompetadminController_blog_category' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				    'js/dashboard/cms.js',
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_content' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				    'js/dashboard/cms.js',
				    'vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js',
				],
				'DompetadminController_content_add' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
					'js/dashboard/cms.js',
					'vendor/ckeditor/ckeditor.js',
					'vendor/select2/js/select2.full.min.js',
					'js/plugins-init/select2-init.js',
					'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_email_template' => [
					'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
					'js/dashboard/cms.js',
					'vendor/chart-js/chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_index_3' => [
					'vendor/chart-js/chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/nouislider/nouislider.min.js',
					'vendor/wnumb/wNumb.js',
					'vendor/peity/jquery.peity.min.js',
					'js/dashboard/dashboard-3.js',
				],
				'DompetadminController_index_4' => [
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				    'vendor/nouislider/nouislider.min.js',
				    'vendor/wnumb/wNumb.js',
				    'vendor/peity/jquery.peity.min.js',
				    'vendor/swiper/js/swiper-bundle.min.js',
				    'js/dashboard/dashboard-4.js',
				],
				'DompetadminController_index_5' => [
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				    'vendor/nouislider/nouislider.min.js',
				    'vendor/wnumb/wNumb.js',
				    'vendor/peity/jquery.peity.min.js',
				    'vendor/swiper/js/swiper-bundle.min.js',
				    'js/dashboard/dashboard-5.js',
				],
				'DompetadminController_index_6' => [
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				    'vendor/peity/jquery.peity.min.js',
				    'js/dashboard/dashboard-6.js',
				],
				'DompetadminController_index_7' => [
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				    'vendor/peity/jquery.peity.min.js',
				    'js/dashboard/dashboard-7.js',
				],
				'DompetadminController_index_8' => [
				    'vendor/chart-js/chart.bundle.min.js',
				    'vendor/apexchart/apexchart.js',
				    'vendor/peity/jquery.peity.min.js',
				    'vendor/swiper/js/swiper-bundle.min.js',
				    'js/dashboard/dashboard-8.js',
				],
				'DompetadminController_menu' => [
				    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
				    'js/dashboard/cms.js',
				    'vendor/nestable2/js/jquery.nestable.min.js',
				    'js/plugins-init/nestable-init.js',
				    'vendor/ckeditor/ckeditor.js',
				    'vendor/apexchart/apexchart.js',
				],
				'DompetadminController_widget_card' => [
				    'vendor/chart-js/chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js',
					'vendor/peity/jquery.peity.min.js',
					'js/plugins-init/piety-init.js',
					'js/plugins-init/widgets-script-init.js',
				],
				'DompetadminController_widget_chart' => [
				    'vendor/chart-js/chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js',
					'vendor/peity/jquery.peity.min.js',
					'js/plugins-init/piety-init.js',
					'js/plugins-init/widgets-script-init.js',
				],
				'DompetadminController_widget_list' => [
				    'vendor/chart-js/chart.bundle.min.js',
					'vendor/apexchart/apexchart.js',
					'vendor/chartist/js/chartist.min.js',
					'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
					'vendor/flot/jquery.flot.js',
					'vendor/flot/jquery.flot.pie.js',
					'vendor/flot/jquery.flot.resize.js',
					'vendor/flot-spline/jquery.flot.spline.min.js',
					'vendor/jquery-sparkline/jquery.sparkline.min.js',
					'js/plugins-init/sparkline-init.js',
					'vendor/peity/jquery.peity.min.js',
					'js/plugins-init/piety-init.js',
					'js/plugins-init/widgets-script-init.js',
				    
				],
					
			]
		],
	]
];
