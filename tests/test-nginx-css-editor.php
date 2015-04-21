<?php

class Nginx_CSS_Editor_Test extends WP_UnitTestCase {

	public function setup()
	{
		$this->css = new Nginx_CSS_Editor();
	}

	/**
	 * @test
	 */
	function mobile_detect()
	{
		/*
		 * $_SERVER['HTTP_X_UA_DETECT'] is undefined
		 */
		$this->assertSame( '', $this->css->mobile_detect() );

		/*
		 * $_SERVER['HTTP_X_UA_DETECT'] is defined and retrurned the value.
		 */
		$_SERVER['HTTP_X_UA_DETECT'] = '@smartphone';
		$this->assertSame( '@smartphone', $this->css->mobile_detect() );

		/*
		 * There is filter hook
		 */
		$_SERVER['HTTP_X_UA_DETECT'] = '@smartphone';
		add_filter( 'nginxmobile_mobile_detect', function(){
			return 'filtered!!';
		} );
		$this->assertSame( 'filtered!!', $this->css->mobile_detect() );
	}

	/**
	 * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 */
	function is_mobile()
	{
		$this->assertSame( false, $this->css->is_mobile() );
		/*
		 * $_SERVER['HTTP_X_UA_DETECT'] is defined and retrurned the value.
		 */
		$_SERVER['HTTP_X_UA_DETECT'] = '@smartphone';
		$this->assertSame( true, $this->css->is_mobile() );

		/*
		 * $_SERVER['HTTP_X_UA_DETECT'] is defined and retrurned the value.
		 */
		$_SERVER['HTTP_X_UA_DETECT'] = '@ktai';
		$this->assertSame( true, $this->css->is_mobile() );
	}

	/**
	 * Tested on the PC
	 *
	 * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 */
	function wp_head_01()
	{
		update_option( 'nginx-css-editor-pc-style', "pc style" );
		update_option( 'nginx-css-editor-sp-style', "sp style" );

		$this->expectOutputString( "<style>pc style</style>" );
		$this->css->wp_head();
	}

	/**
	 * Tested on the Smartphone
	 *
	 * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
	 */
	function wp_head()
	{
		update_option( 'nginx-css-editor-pc-style', "pc style" );
		update_option( 'nginx-css-editor-sp-style', "sp style" );

		$_SERVER['HTTP_X_UA_DETECT'] = '@ktai';
		$this->expectOutputString( "<style>sp style</style>" );
		$this->css->wp_head();
	}
}

