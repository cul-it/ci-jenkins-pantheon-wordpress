require 'rubygems'
require 'selenium-cucumber'
require 'rspec/expectations'
require 'capybara/poltergeist'
require 'inifile'
require 'cucumber_priority'
require 'spreewald/development_steps'
require 'spreewald/table_steps'
require 'spreewald/web_steps'
require "selenium/webdriver"
require "phantomjs"

def what_is(element)
  puts "\n********************* what is V\n"
  puts element.inspect
  puts element['innerHTML']
  puts "\n********************* what is ^\n"
end

# read the .ini file
$anyini = IniFile.load('features/site-testing.ini')

# Store command line arguments
$browser_type = ENV['BROWSER'] || $anyini['global'][':default_browser']
$headless_type = ENV['HEADLESS_BROWSER'] || $anyini['global'][':default_headless']
$use_headless = ENV['HEADLESS'] || false
$platform = ENV['PLATFORM'] || 'desktop'
$os_version = ENV['OS_VERSION']
$device_name = ENV['DEVICE_NAME']
$udid = ENV['UDID']
$app_path = ENV['APP_PATH']
$poltergeist_debugging_flag = ENV['POLTERGEIST_DEBUG'] ? 'true' : 'false'

# check for valid parameters
validate_parameters $platform, $browser_type, $app_path

#Selenium::WebDriver::Chrome.driver_path = $anyini['global'][':chromedriver_path']

# If platform is android or ios create driver instance for mobile browser
if $platform == 'android' or $platform == 'iOS'

  if $browser_type == 'native'
    $browser_type = "Browser"
  end

  if $platform == 'android'
    $device_name, $os_version = get_device_info
  end

  desired_caps = {
    caps:       {
      platformName:  $platform,
      browserName: $browser_type,
      versionNumber: $os_version,
      deviceName: $device_name,
      udid: $udid,
      app: ".//#{$app_path}"
      },
    }

  begin
    $driver = Appium::Driver.new(desired_caps).start_driver
  rescue Exception => e
    puts e.message
    Process.exit(0)
  end
else # else create driver instance for desktop browser
  begin
    if $use_headless
      case $headless_type
      when "poltergeist"
        # headless tests with poltergeist/PhantomJS
        options = {
          phantomjs: Phantomjs.path,
          timeout: 30,
          js_errors: false,
          window_size: [1280, 1024],
          debug: false,
          phantomjs_options: [
            '--proxy-type=none',
            '--load-images=no',
            '--ignore-ssl-errors=yes',
            '--ssl-protocol=any',
            '--web-security=false',
            '--debug=' + $poltergeist_debugging_flag,
          ]
          }
        Capybara.register_driver :poltergeist do |app|
          Capybara::Poltergeist::Driver.new(app, options)
        end
        Capybara.default_driver    = :poltergeist
        Capybara.javascript_driver = :poltergeist
      when "selenium_chrome_headless"
        Capybara.register_driver :chrome do |app|
          Capybara::Selenium::Driver.new(app, browser: :chrome)
        end
        
        Capybara.register_driver :headless_chrome do |app|
          capabilities = Selenium::WebDriver::Remote::Capabilities.chrome(
            chromeOptions: { args: %w(headless disable-gpu) }
          )
        
          Capybara::Selenium::Driver.new app,
            browser: :chrome,
            desired_capabilities: capabilities
        end
        Capybara.default_driver    = :headless_chrome
        Capybara.javascript_driver = :headless_chrome
      when "headless_chrome"
        Capybara.register_driver :headless_chrome do |app|
          capabilities = Selenium::WebDriver::Remote::Capabilities.chrome(chromeOptions: { args: %w(headless disable-gpu) })
          Capybara::Selenium::Driver.new app,
            browser: :chrome,
            desired_capabilities: capabilities
        end
        Capybara.default_driver    = :headless_chrome
        Capybara.javascript_driver = :headless_chrome
      when nil
        Capybara.register_driver :chrome do |app|
          Capybara::Selenium::Driver.new(app, browser: :chrome)
        end
        Capybara.default_driver    = :chrome
        Capybara.javascript_driver = :headless_chrome
      end
    else
      Capybara.register_driver :chrome do |app|
        Capybara::Selenium::Driver.new(app, :browser => :chrome)
      end
      Capybara.default_driver    = :chrome
      Capybara.javascript_driver = :chrome
      # $driver = Selenium::WebDriver.for (:"#{$browser_type}")
      # Capybara.default_driver = (:"#{$browser_type}")
      # Capybara.javascript_driver = (:"#{$browser_type}")
      # $driver.manage().window().maximize()
    end

  rescue Exception => e
    puts e.message
    Process.exit(0)
  end
end

def wait_for_ajax
  Timeout.timeout(Capybara.default_max_wait_time) do
    loop until finished_all_ajax_requests?
  end
end

def finished_all_ajax_requests?
  page.evaluate_script('jQuery.active').zero?
end

