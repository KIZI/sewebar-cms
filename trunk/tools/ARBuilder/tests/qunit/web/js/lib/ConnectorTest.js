
module('Connector');

test('Instantiation', function () {
	var connector = new Connector('getData.php', 'setData.php', 'getRules.php');
	
	equal(connector.getDataGetURL(), 'getData.php');
	equal(connector.getDataSetURL(), 'setData.php');
	equal(connector.getRulesGetURL(), 'getRules.php');
});

test('getData', function () {
	var dataContainer = new DataContainer();
	var connector = new Connector('../../../../../web/getData.php');
	var mock = this.mock(dataContainer);
	mock.expects('parseData').once();

	connector.getData(dataContainer);
	ok(mock.verify(), 'getData success.');
});