package xquerysearch.settings;

import junit.framework.Assert;

import org.junit.Before;
import org.junit.Test;

import xquerysearch.domain.Settings;

public class SettingsManagerTest {
	
	Settings setMan;
	
	@Before
	public void fillSettingsManager() {
		setMan = new Settings();
		setMan.setQueriesDirectory("/directory/queries");
		setMan.setTemporaryDirectory("/directory/temporary");
		setMan.setEnvironmentDirectory("/directory/environment");
		setMan.setContainerName("containerName");
		setMan.setUseTransformation(true);
		setMan.setPmmlTransformationPath("/transformation/pmml");
		setMan.setBkefTransformationPath("/transformation/bkef");
		setMan.setValidationSchemaPath("/validation");
	}
	
	@Test
	public void regularTest() {
		Assert.assertEquals("/directory/queries", setMan.getQueriesDirectory());
		Assert.assertEquals("/directory/temporary", setMan.getTemporaryDirectory());
		Assert.assertEquals("/directory/environment", setMan.getEnvironmentDirectory());
		Assert.assertEquals("containerName", setMan.getContainerName());
		Assert.assertEquals(true, setMan.getUseTransformationBool());
		Assert.assertEquals("/transformation/pmml", setMan.getPmmlTransformationPath());
		Assert.assertEquals("/transformation/bkef", setMan.getBkefTransformationPath());
		Assert.assertEquals("/validation", setMan.getValidationSchemaPath());
	}
	
	@Test
	public void changeSettingsTest() {
		Settings setManChng = new Settings();
		
	}

}
