package xquerysearch.utils;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.logging.Logger;

import xquerysearch.controllers.MainController;

/**
 * 
 * @author Tomas Marek
 */
public class SettingsFileUtils {
	
	private static Logger logger = MainController.getLogger();
	
	/**
     * 
	 * @return settings file, if not found or error occured then <code>null</code>
	 */
	public static File getSettingsFile() {
		File settingsFile = new File(MainController.getSettingsFileName());
		if (!settingsFile.exists()) {
			return createSettingsFile();
		}			
		return settingsFile;
	}
	
	private static File createSettingsFile() {
		try {
			File settingFile = new File(MainController.getSettingsFileName());
			settingFile.createNewFile();
			String newFileOutput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
				+ "\n<settings>"
				+ "\n\t<envDir></envDir>"
				+ "\n\t<queryDir></queryDir>"
				+ "\n\t<containerName></containerName>"
				+ "\n\t<useTransformation></useTransformation> <!-- true / false -->"
				+ "\n\t<transformationPathPMML></transformationPathPMML>"
				+ "\n\t<transformationPathBKEF></transformationPathBKEF>"
				+ "\n\t<tempDir></tempDir>"
				+ "\n\t<schemaPath></schemaPath>" + "\n</settings>";

			FileOutputStream fos = new FileOutputStream(settingFile);
			OutputStreamWriter osw = new OutputStreamWriter(fos);
			osw.write(newFileOutput);
			osw.close();
			fos.close();
			
			return settingFile;
		} catch (IOException e) {
			logger.warning("Creating settings file failed! - IO exception");
			return null;
		}
	}

}
