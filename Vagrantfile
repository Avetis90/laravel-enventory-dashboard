Vagrant.configure(2) do |config|
  config.vm.define 'smartecom'
  config.vm.box = 'centos69'
  config.vm.box_url = 'https://s3-eu-west-1.amazonaws.com/hwdmedia-resources/boxes/centos69.box'

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.aliases = %w(node.smartecom.vm)
  config.vm.hostname = 'smartecom.vm'

  [80, 3306].each do |port|
    config.vm.network :forwarded_port, guest: port, host: port, host_ip: 'localhost'
  end

  config.vm.synced_folder '.', '/home/vagrant/htdocs', :mount_options => %w(dmode=775 fmode=664)

  config.vm.provision :shell, :name => 'provision.sh', :path => 'vagrant/provision.sh'
  config.vm.provision :shell, :name => 'application.sh', :path => 'vagrant/application.sh', :privileged => false
  config.vm.provision :shell, :name => 'autostart.sh', :path => 'vagrant/autostart.sh', :run => :always, :privileged => false
end
