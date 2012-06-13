all: lumi

lumi: lumi.php Classes/* Functions/*
	php ./lumi.php > lumi; chmod u+x lumi