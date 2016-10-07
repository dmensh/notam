casper.test.begin('Testing NOTAM lookup', 2, function(test){
    casper.start('http://localhost');

    casper.then(function(){
        test.assertTitle('Rocket Route', 'Page has correct title');
    });

    casper.then(function() {
        this.echo("Waiting for map to load");
        this.wait(3000, function() {
            this.sendKeys('#icao', 'KJFK');
            this.page.sendEvent("keypress", this.page.event.key.Enter);
            this.echo("Filled in ICAO field");
        });
    });

    casper.then(function() {
        this.echo("Waiting for markers to populate");
        this.wait(3000, function() {
            test.assertExists('.notam-marker');
        });
    });

    casper.run(function(){
        test.done();
    })
});