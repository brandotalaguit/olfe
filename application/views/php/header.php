<header>
      <!-- Static navbar -->
      <div class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Administrator Dashboard</a>
          </div>
          <div class="navbar-collapse collapse">
            <div class="navbar-form navbar-right">
              <div class="input-group">
                <a href="<?php echo base_url('administrator/logout'); ?>" class="btn btn-danger"><i class="fa fa-power-off "></i> Logout</a>
              </div>
            </div>
            <p class="navbar-text navbar-right">Signed in as <b><?php echo $this->session->userdata("userlogin"); ?></b></p>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

</header>