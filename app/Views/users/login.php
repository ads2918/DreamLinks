<section>
	<form action="/user/login" method="post">
		<?= csrf_field() ?>
		<div class="mask d-flex align-items-center h-100 gradient-custom-3">
			<div class="container h-100">
				<div class="row d-flex justify-content-center align-items-center h-100">	
					<div class="col-12 col-md-9 col-lg-7 col-xl-6">			
						<div class="card" style="border-radius: 15px;">
							<div class="card-body p-5">
								<h2 class="text-center mb-5">Login</h2>
 
								<div data-mdb-input-init class="form-outline mb-4">
								  <input type="text" name="email" id="form3Example1cg" value="<?= set_value('email') ?>" class="form-control form-control-lg" />
								  <label class="form-label" for="form3Example1cg">Email</label>
								</div>
								<div data-mdb-input-init class="form-outline mb-4">
								  <input type="password" name="password" id="form3Example4cg" value="<?= set_value('password') ?>" class="form-control form-control-lg" />
								  <label class="form-label" for="form3Example4cg">Password</label>
								</div>
								<div class="d-flex justify-content-center">
								  <button  type="submit" data-mdb-button-init
									data-mdb-ripple-init class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Login</button>
								</div>
								 <p class="text-center text-muted mt-5 mb-0">Do not have an account? <a href="/user/register"
									class="fw-bold text-body"><u>Register</u></a></p>
								 <p class="text-center text-muted mt-5 mb-0">Forget Password? <a href="/user/forgot-password/"
									class="fw-bold text-body"><u>Reset</u></a></p>	
									
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>

 
 
