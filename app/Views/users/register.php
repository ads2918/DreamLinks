<section>
  <form action="/user" method="post">
  <?= csrf_field() ?>
  <div class="mask d-flex align-items-center h-100 gradient-custom-3">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
          <div class="card" style="border-radius: 15px;">
            <div class="card-body p-5">
              <h2 class=" text-center mb-5">
				Who are you....?
			  </h2>
                <div data-mdb-input-init class="form-outline mb-4">
					<input type="email" name="email" id="email" value="<?= set_value('email') ?>" class="form-control form-control-lg" />
					<label class="form-label" for="email">Your Email</label>
                </div>
				 <div data-mdb-input-init class="form-outline mb-4">
					<input type="text" name="username" id="username" value="<?= set_value('username') ?>" class="form-control form-control-lg" />
					<label class="form-label" for="username">Your Username</label>
                </div>
                <div data-mdb-input-init class="form-outline mb-4">
					<input type="password" name="password" id="password" value="" class="form-control form-control-lg" />
					<label class="form-label" for="password">Password</label>
                </div>
                <div data-mdb-input-init class="form-outline mb-4">
					<input type="password" name="password2" id="password2" value="" class="form-control form-control-lg" />
					<label class="form-label" for="password2">Repeat your password</label>
                </div>
                <div class="d-flex justify-content-center">
                  <button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Register</button>
                </div>

                <p class="text-center text-muted mt-5 mb-0">Already told us? <a href="/user/login"
                    class="fw-bold text-body"><u>Login here</u></a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
 </form>
 
 
