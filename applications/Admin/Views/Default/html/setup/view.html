<div class="row">
	<div class="col">
		<div class="card mt-2 mb-2">
			<div class="card-header bg-primary text-white text-center font-weight-bold text-uppercase"></div>
			<div class="card-body bg-white">
				<div class="row justify-content-center p-3">
					<div class="col-6">
						<div class="form-group">
							<label for="host">Host *</label>
							<div class="input-group">
								<input type="text" name="host" id="host" class="form-control" value="localhost">
							</div>
						</div>
						<div class="form-group">
							<label for="port">Port *</label>
							<div class="input-group">
								<input type="text" name="port" id="port" class="form-control" value="3306">
							</div>
						</div>
						<div class="form-group">
							<label for="database_name">Database Name *</label>
							<div class="input-group">
								<input type="text" name="database_name" id="database_name" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="username">Username *</label>
							<div class="input-group">
								<input type="text" name="username" id="username" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="password">Password *</label>
							<div class="input-group">
								<input type="password" name="password" id="password" class="form-control">
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="form-group">
									<div class="checkbox icheck-danger">
										<input type="checkbox" name="drop" id="drop">
										<label for="drop">
											Drop tables if exists
										</label>
									</div>
								</div>
							</div>
							<div class="col text-center">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn btn-secondary">
										<input type="radio" name="mode-options" class="mode-options" id="production">Production
									</label>
									<label class="btn btn-primary active">
										<input type="radio" name="mode-options" class="mode-options" id="development" checked>Development
									</label>
								</div>
							</div>
						</div>
						<hr>
						<button id="submit" class="btn btn-primary">
							<span id="submit-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" hidden></span>
							Create Schema
						</button>
					</div>
				</div>
				<div class="row text-center m-2" id="next-div" hidden>
					<div class="col">
						<a href='/admin/modules' id="next" class="btn btn-success" disabled=""><i class="fa fa-check"></i> Install Modules</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.card-header').html('Hello World Framework Setup : MySQL Database Schema');
	});

	$('#submit').click(function() {
		$(this).attr('disabled', true);
		$('#submit-spinner').attr('hidden', false);
		$('#alert').attr('hidden', true);
		if ($('#host').val() === '') {
			$('#host').addClass('is-invalid');
			$('#host').focus(function() {
				$(this).removeClass('is-invalid');
			});
			$('#submit-spinner').attr('hidden', true);
			$('#submit').attr('disabled', false);
		} else if ($('#port').val() === '') {
			$('#port').addClass('is-invalid');
			$('#port').focus(function() {
				$(this).removeClass('is-invalid');
			});
			$('#submit-spinner').attr('hidden', true);
			$('#submit').attr('disabled', false);
		} else if ($('#database_name').val() === '') {
			$('#database_name').addClass('is-invalid');
			$('#database_name').focus(function() {
				$(this).removeClass('is-invalid');
			});
			$('#submit-spinner').attr('hidden', true);
			$('#submit').attr('disabled', false);
		} else if ($('#username').val() === '') {
			$('#username').addClass('is-invalid');
			$('#username').focus(function() {
				$(this).removeClass('is-invalid');
			});
			$('#submit-spinner').attr('hidden', true);
			$('#submit').attr('disabled', false);
		} else if ($('#password').val() === '') {
			$('#password').addClass('is-invalid');
			$('#password').focus(function() {
				$(this).removeClass('is-invalid');
			});
			$('#submit-spinner').attr('hidden', true);
			$('#submit').attr('disabled', false);
		} else {
			$.ajax({
				type    : 'POST',
				url     : '/admin/setup/run/',
				data    :
					{
						"host"          : $('#host').val(),
						"port"          : $('#port').val(),
						"database_name" : $('#database_name').val(),
						"username"      : $('#username').val(),
						"password"      : $('#password').val(),
						"drop"          : $('#drop')[0].checked,
						"mode"          : $("input[name='mode-options']:checked")[0].id
					},
				dataType: 'json',
				success: function (data) {
					if (data && data.responseCode === 0) {
						$('#alert').attr('hidden', false);
						$('#alert').html('');
						$('#alert').removeClass (function (index, className) {
							return (className.match (/(^|\s)alert-\S+/g) || []).join(' ');
						}).addClass('alert-success').html(data.responseMessage);
						$('#next').attr('disabled', false);
						$('#next-div').attr('hidden', false);
					} else if (data.responseCode === 1) {
						$('#alert').attr('hidden', false);
						$('#alert').html('');
						$('#alert').removeClass (function (index, className) {
							return (className.match (/(^|\s)alert-\S+/g) || []).join(' ');
						}).addClass('alert-danger').html(data.responseMessage);
						$('#submit').attr('disabled', false);
					}
					$('#submit-spinner').attr('hidden', true);
				},
				error: function (data) {
					if (data.responseJSON && data.responseJSON.responseCode === 1) {
						$('#alert').attr('hidden', false);
						$('#alert').html('');
						$('#alert').removeClass (function (index, className) {
							return (className.match (/(^|\s)alert-\S+/g) || []).join(' ');
						}).addClass('alert-danger').html(data.responseJSON.responseMessage);
					}
					$('#submit').attr('disabled', false);
					$('#submit-spinner').attr('hidden', true);
				}
			});
		}
	});

	$('.mode-options').each(function(index, option) {
		$($(option).parent()).click(function() {
			var selectedOptionId = $(this).children()[0].id;

			if (selectedOptionId === 'production') {
				$('#' + selectedOptionId).parent().addClass('btn-primary').removeClass('btn-secondary');
				$('#development').parent().removeClass('btn-primary').addClass('btn-secondary');
			}

			if (selectedOptionId === 'development') {
				$('#' + selectedOptionId).parent().addClass('btn-primary').removeClass('btn-secondary');
				$('#production').parent().removeClass('btn-primary').addClass('btn-secondary');
			}
		});
	});
</script>