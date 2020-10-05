<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Hello World Framework</title>
		<?= $this->assets->outputCss('headLinks') ?>
		<?= $this->assets->outputJs('headJs') ?>
		<?= $this->assets->outputInlineCss() ?>
	</head>
	<body>
		<div class="main-content">
			<div class="container-fluid">
				<div class="row">
					<div class="col">
						<div id="alert" class="alert mt-2 mb-2" hidden></div>
					</div>
				</div>
				<?= $this->getContent() ?>
			</div>
		</div>
	</body>
	<footer class="footer">
		<?= $this->assets->outputJs('footerJs') ?>
		<?= $this->assets->outputInlineJs() ?>
	</footer>
</html>