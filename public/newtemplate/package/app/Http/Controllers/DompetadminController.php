<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DompetadminController extends Controller
{
    // Dashboard 
    public function dashboard()
    {

        $page_title = 'Dashboard';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.index', compact('page_title', 'page_description'));
    }
    
    // Dashboard 2
    public function dashboard_2()
    {

        $page_title = 'Dashboard';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.index_2', compact('page_title', 'page_description'));
    }
    
    // my_wallet
    public function my_wallet()
    {

        $page_title = 'My Wallet';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.my_wallet', compact('page_title', 'page_description'));
    }

    // page invoices
    public function page_invoices()
    {

        $page_title = 'Invoices';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.page_invoices', compact('page_title', 'page_description'));
    }

    // cards_center
    public function cards_center()
    {

        $page_title = 'Cards Center';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.cards_center', compact('page_title', 'page_description'));
    }

     // page transaction
    public function page_transaction()
    {

        $page_title = 'Transaction History';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.page_transaction', compact('page_title', 'page_description'));
    }

    // transaction_details
    public function transaction_details()
    {

        $page_title = 'Transaction Details';
        $page_description = 'Some description for the page';
        
        return view('dompet.dashboard.transaction_details', compact('page_title', 'page_description'));
    }

    // profile
    public function app_profile()
    {
        
        $page_title = 'App Profile';
        $page_description = 'Some description for the page';

        return view('dompet.app.profile', compact('page_title', 'page_description' ));   
    }

    // Post Details
    public function post_details()
    {
        
        $page_title = 'Post Details';
        $page_description = 'Some description for the page';

        return view('dompet.app.post_details', compact('page_title', 'page_description' ));     
    }

    // Email Compose
    public function email_compose()
    {
        $page_title = 'Email Compose';
        $page_description = 'Some description for the page';
        return view('dompet.message.compose', compact('page_title', 'page_description' ));
    }
    
    // Email Inbox
    public function email_inbox()
    {
        $page_title = 'Email Inbox';
        $page_description = 'Some description for the page';
        return view('dompet.message.inbox', compact('page_title', 'page_description' ));
    }
    
    // Email Read
    public function email_read()
    {
        $page_title = 'Email Read';
        $page_description = 'Some description for the page';
        return view('dompet.message.read', compact('page_title', 'page_description' ));
    }

    // Calender
    public function app_calender()
    {
        $page_title = 'App Calendar';
        $page_description = 'Some description for the page';
        return view('dompet.app.calender', compact('page_title', 'page_description' ));
    }

    // Ecommerce Checkout
    public function ecom_checkout()
    {
        $page_title = 'Ecom Checkout';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.checkout', compact('page_title', 'page_description' ));
    }

    // Ecommerce Customers
    public function ecom_customers()
    {
        $page_title = 'Ecom Customers';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.customers', compact('page_title', 'page_description' ));
    }
    
    // Ecommerce Invoice
    public function ecom_invoice()
    {
        $page_title = 'Ecom Invoice';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.invoice', compact('page_title', 'page_description' ));
    }
    
    // Ecommerce Product Detail
    public function ecom_product_detail()
    {
        $page_title = 'Product Detail';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.product_detail', compact('page_title', 'page_description' ));
    }
    
    // Ecommerce Product Grid
    public function ecom_product_grid()
    {
        $page_title = 'Product Grid';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.product_grid', compact('page_title', 'page_description' ));
    }
    
    // Ecommerce Product List
    public function ecom_product_list()
    {
        $page_title = 'Product List';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.product_list', compact('page_title', 'page_description' ));
    }
    
    // Ecommerce Product Order
    public function ecom_product_order()
    {
        $page_title = 'Product Order';
        $page_description = 'Some description for the page';
        return view('dompet.ecommerce.product_order', compact('page_title', 'page_description' ));
    }

    // Chart Chartist
    public function chart_chartist()
    {
        $page_title = 'Chart Chartist';
        $page_description = 'Some description for the page';
        return view('dompet.chart.chartist', compact('page_title', 'page_description' ));
    }
    
    // Chart Chartjs
    public function chart_chartjs()
    {
        $page_title = 'Chart ChartJs';
        $page_description = 'Some description for the page';
        
        return view('dompet.chart.chartjs', compact('page_title', 'page_description' ));
    }
    
    // Chart Flot
    public function chart_flot()
    {
        $page_title = 'Chart Flot';
        $page_description = 'Some description for the page';
        
        return view('dompet.chart.flot', compact('page_title', 'page_description' ));
    }
    
    // Chart Morris
    public function chart_morris()
    {
        $page_title = 'Chart Morris';
        $page_description = 'Some description for the page';
        
        return view('dompet.chart.morris', compact('page_title', 'page_description' ));
    }
    
    // Chart Peity
    public function chart_peity()
    {
        $page_title = 'Chart Peity';
        $page_description = 'Some description for the page';
        
        return view('dompet.chart.peity', compact('page_title', 'page_description' ));
    }
    
    // Chart Sparkline
    public function chart_sparkline()
    {
        $page_title = 'Chart Sparkline';
        $page_description = 'Some description for the page';
        
        return view('dompet.chart.sparkline', compact('page_title', 'page_description' ));
    }

        // Ui Accordion
    public function ui_accordion()
    {
        $page_title = 'Accordion';
        $page_description = 'Some description for the page';
        return view('dompet.ui.accordion', compact('page_title', 'page_description' ));
    }
    
    // Ui Alert
    public function ui_alert()
    {
        $page_title = 'Alert';
        $page_description = 'Some description for the page';
        return view('dompet.ui.alert', compact('page_title', 'page_description' ));
    }
    
    // Ui Badge
    public function ui_badge()
    {
        $page_title = 'Badge';
        $page_description = 'Some description for the page';
        return view('dompet.ui.badge', compact('page_title', 'page_description' ));
    }
    
    // Ui Button
    public function ui_button()
    {
        $page_title = 'Button';
        $page_description = 'Some description for the page';
        return view('dompet.ui.button', compact('page_title', 'page_description' ));
    }
    
    // Ui Button Group
    public function ui_button_group()
    {
        $page_title = 'Button Group';
        $page_description = 'Some description for the page';
        return view('dompet.ui.button_group', compact('page_title', 'page_description' ));
    }
    
    // Ui Card
    public function ui_card()
    {
        $page_title = 'Card';
        $page_description = 'Some description for the page';
        return view('dompet.ui.card', compact('page_title', 'page_description' ));
    }
    
    // Ui Carousel
    public function ui_carousel()
    {
        $page_title = 'Carousel';
        $page_description = 'Some description for the page';
        return view('dompet.ui.carousel', compact('page_title', 'page_description' ));
    }
    
    // Ui Dropdown
    public function ui_dropdown()
    {
        $page_title = 'Dropdown';
        $page_description = 'Some description for the page';
        return view('dompet.ui.dropdown', compact('page_title', 'page_description' ));
    }
    
    // Ui Grid
    public function ui_grid()
    {
        $page_title = 'Grid';
        $page_description = 'Some description for the page';
        return view('dompet.ui.grid', compact('page_title', 'page_description' ));
    }
    
    // Ui List Group
    public function ui_list_group()
    {
        $page_title = 'List Group';
        $page_description = 'Some description for the page';
        return view('dompet.ui.list_group', compact('page_title', 'page_description' ));
    }
    
    // Ui Modal
    public function ui_modal()
    {
        $page_title = 'Modal';
        $page_description = 'Some description for the page';
        return view('dompet.ui.modal', compact('page_title', 'page_description' ));
    }
    
    // Ui Pagination
    public function ui_pagination()
    {
        $page_title = 'Pagination';
        $page_description = 'Some description for the page';
        return view('dompet.ui.pagination', compact('page_title', 'page_description' ));
    }
    
    // Ui Popover
    public function ui_popover()
    {
        $page_title = 'Popover';
        $page_description = 'Some description for the page';
        return view('dompet.ui.popover', compact('page_title', 'page_description' ));
    }
    
    // Ui Progressbar
    public function ui_progressbar()
    {
        $page_title = 'Progressbar';
        $page_description = 'Some description for the page';
        return view('dompet.ui.progressbar', compact('page_title', 'page_description' ));
    }
    
    // Ui Tab
    public function ui_tab()
    {
        $page_title = 'Tab';
        $page_description = 'Some description for the page';
        return view('dompet.ui.tab', compact('page_title', 'page_description' ));
    }
    

    // Ui Typography
    public function ui_typography()
    {
        $page_title = 'Typography';
        $page_description = 'Some description for the page';
        return view('dompet.ui.typography', compact('page_title', 'page_description' ));
    }

    // UC Nestedable.
    public function uc_nestable()
    {
        $page_title = 'Nestable';
        $page_description = 'Some description for the page';
        return view('dompet.uc.nestable', compact('page_title', 'page_description' ));
    }
    // UC Lightgallery.
    public function uc_lightgallery()
    {
        $page_title = 'LightGallery';
        $page_description = 'Some description for the page';
        return view('dompet.uc.lightgallery', compact('page_title', 'page_description' ));
    }
    
    // UC NoUi Slider
    public function uc_noui_slider()
    {
        $page_title = 'Noui Slider';
        $page_description = 'Some description for the page';
        return view('dompet.uc.noui_slider', compact('page_title', 'page_description' ));
    }
    
    // UC Select2
    public function uc_select2()
    {
        $page_title = 'Select2';
        $page_description = 'Some description for the page';
        return view('dompet.uc.select2', compact('page_title', 'page_description' ));
    }
    
    // UC Sweetalert
    public function uc_sweetalert()
    {
        $page_title = 'SweetAlert';
        $page_description = 'Some description for the page';
        return view('dompet.uc.sweetalert', compact('page_title', 'page_description' ));
    }
    
    // UC Toastr
    public function uc_toastr()
    {
        $page_title = 'Toastr';
        $page_description = 'Some description for the page';
        return view('dompet.uc.toastr', compact('page_title', 'page_description' ));
    }

    // Map Jqvmap
    public function map_jqvmap()
    {
        $page_title = 'Map Jqvmap';
        $page_description = 'Some description for the page';
        return view('dompet.map.jqvmap', compact('page_title', 'page_description' ));
    }

    // Widget Basic
    public function widget_basic()
    {
        $page_title = 'Widget';
        $page_description = 'Some description for the page';
        return view('dompet.widget.widget_basic', compact('page_title', 'page_description' ));
    }

    // Form ckeditor 
    public function form_ckeditor()
    {
        $page_title = 'Form CkEditor';
        $page_description = 'Some description for the page';
        return view('dompet.form.ckeditor', compact('page_title', 'page_description' ));
    }
    
    // Form Element
    public function form_element()
    {
        $page_title = 'Form Element';
        $page_description = 'Some description for the page';
        return view('dompet.form.element', compact('page_title', 'page_description' ));
    }
    
    // Form Pickers
    public function form_pickers()
    {
        $page_title = 'Form Pickers';
        $page_description = 'Some description for the page';
        return view('dompet.form.pickers', compact('page_title', 'page_description' ));
    }
    
    // Form Validation
    public function form_validation()
    {
        $page_title = 'Form Validat';
        $page_description = 'Some description for the page';
        return view('dompet.form.validation', compact('page_title', 'page_description' ));
    }
    
    // Form Wizard
    public function form_wizard()
    {
        $page_title = 'Form Wizard';
        $page_description = 'Some description for the page';
        return view('dompet.form.wizard', compact('page_title', 'page_description' ));
    }

    // Table Bootstrap Basic
    public function table_bootstrap_basic()
    {
        $page_title = 'Table Basic';
        $page_description = 'Some description for the page';
        return view('dompet.table.bootstrap_basic', compact('page_title', 'page_description' ));
    }
    
    // Table Datatable Basic
    public function table_datatable_basic()
    {
        $page_title = 'Table Datatable';
        $page_description = 'Some description for the page';
        return view('dompet.table.datatable_basic', compact('page_title', 'page_description' ));
    }

        // Page Error 400
    public function page_error_400()
    {
        $page_title = 'Page Error 400';
        $page_description = 'Some description for the page';
        return view('dompet.page.error_400', compact('page_title', 'page_description' ));
    }
    
    // Page Error 403
    public function page_error_403()
    {
        $page_title = 'Page Error 403';
        $page_description = 'Some description for the page';
        return view('dompet.page.error_403', compact('page_title', 'page_description' ));
    }
    
    // Page Error 404
    public function page_error_404()
    {
        $page_title = 'Page Error 404';
        $page_description = 'Some description for the page';
        return view('dompet.page.error_404', compact('page_title', 'page_description' ));
    }
    
    // Page Error 500
    public function page_error_500()
    {
        $page_title = 'Page Error 500';
        $page_description = 'Some description for the page';
        return view('dompet.page.error_500', compact('page_title', 'page_description' ));
    }
    
    // Page Error 503
    public function page_error_503()
    {
        $page_title = 'Page Error 503';
        $page_description = 'Some description for the page';
        return view('dompet.page.error_503', compact('page_title', 'page_description' ));
    }
    
    // Page Forgot Password
    public function page_forgot_password()
    {
        $page_title = 'Page Forgot Password';
        $page_description = 'Some description for the page';
        return view('dompet.page.forgot_password', compact('page_title', 'page_description' ));
    }
    
    // Page Lock Screen
    public function page_lock_screen()
    {
        $page_title = 'Page Lock Screen';
        $page_description = 'Some description for the page';
        return view('dompet.page.lock_screen', compact('page_title', 'page_description' ));
    }

    // Page Login
    public function page_login()
    {
        $page_title = 'Page Login';
        $page_description = 'Some description for the page';
        return view('dompet.page.login', compact('page_title', 'page_description' ));
    }
    
    // Page Register
    public function page_register()
    {
        $page_title = 'Page Register';
        $page_description = 'Some description for the page';
        return view('dompet.page.register', compact('page_title', 'page_description' ));
    }
     // empty page
    public function empty_page()
    {
        $page_title = 'Empty Page';
        $page_description = 'Some description for the page';
        return view('dompet.page.empty', compact('page_title', 'page_description' ));
    }


    public function add_email()
    {
        $page_title = 'Add Email';
        $page_description = 'Some description for the page';
        return view('dompet.message.add_email', compact('page_title', 'page_description' ));
    }
    public function add_blog()
    {
        $page_title = 'Add Blog';
        $page_description = 'Some description for the page';
        return view('dompet.blog.add_blog', compact('page_title', 'page_description' ));
    }
    public function blog()
    {
        $page_title = 'Blog';
        $page_description = 'Some description for the page';
        return view('dompet.blog.blog', compact('page_title', 'page_description' ));
    }
    public function blog_category()
    {
        $page_title = 'Blog Category';
        $page_description = 'Some description for the page';
        return view('dompet.blog.blog_category', compact('page_title', 'page_description' ));
    }
    public function content()
    {
        $page_title = 'Content';
        $page_description = 'Some description for the page';
        return view('dompet.content.content', compact('page_title', 'page_description' ));
    }
    public function content_add()
    {
        $page_title = 'Content Add';
        $page_description = 'Some description for the page';
        return view('dompet.content.content_add', compact('page_title', 'page_description' ));
    }
    public function email_template()
    {
        $page_title = 'Email Template';
        $page_description = 'Some description for the page';
        return view('dompet.message.email_template', compact('page_title', 'page_description' ));
    }
    public function index_3()
    {
        $page_title = 'Dashboard 3';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_3', compact('page_title', 'page_description' ));
    }
    public function index_4()
    {
        $page_title = 'Dashboard 4';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_4', compact('page_title', 'page_description' ));
    }
    public function index_5()
    {
        $page_title = 'Dashboard 5';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_5', compact('page_title', 'page_description' ));
    }
    public function index_6()
    {
        $page_title = 'Dashboard 6';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_6', compact('page_title', 'page_description' ));
    }
    public function index_7()
    {
        $page_title = 'Dashboard 7';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_7', compact('page_title', 'page_description' ));
    }
    public function index_8()
    {
        $page_title = 'Dashboard 8';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.index_8', compact('page_title', 'page_description' ));
    }
    public function menu()
    {
        $page_title = 'Menu';
        $page_description = 'Some description for the page';
        return view('dompet.dashboard.menu', compact('page_title', 'page_description' ));
    }
    public function widget_card()
    {
        $page_title = 'Widget Card';
        $page_description = 'Some description for the page';
        return view('dompet.widget.card', compact('page_title', 'page_description' ));
    }
    public function widget_chart()
    {
        $page_title = 'Widget Chart';
        $page_description = 'Some description for the page';
        return view('dompet.widget.chart', compact('page_title', 'page_description' ));
    }
    public function widget_list()
    {
        $page_title = 'Widget List';
        $page_description = 'Some description for the page';
        return view('dompet.widget.list', compact('page_title', 'page_description' ));
    }
}
