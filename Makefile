all: lumi

lumi: lumi.php Classes/* Functions/*
	./ppp.sh lumi.php lumi; chmod u+x lumi

clean:
	rm -f lumi

install: lumi
	install lumi /usr/local/bin/