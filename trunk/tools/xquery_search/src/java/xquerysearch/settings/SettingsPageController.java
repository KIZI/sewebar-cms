package xquerysearch.settings;

public class SettingsPageController {
	
	/**
	 * Builds settings page
	 * @param settings
	 * @return settings page as <code>HTML</code>
	 */
	public static String createSettingsPage(SettingsManager settings) {
		String TR = " selected=\"selected\"";
		String FL = "";
		if (!settings.isUseTransformation()) {
			TR = "";
			FL = " selected=\"selected\"";
		} else {
			TR = " selected=\"selected\"";
			FL = "";
		}
		return  
				"\n<html><body>" +
						"\n<h2>Settings</h2> " +
						"\n<div id=\"settings\">" +
						"\n<table>" +
						"\n<form method=\"post\" action=\"xquery_servlet\">" +
						"\n<input type=\"hidden\" name=\"action\" value=\"writesettings\">" +
						"\n<tr><td>DB Environment directory:</td><td><input type=\"text\" name=\"envDir\" value=\""+ settings.getEnvironmentDirectory() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>Query directory:</td><td><input type=\"text\" name=\"queryDir\" value=\""+ settings.getQueriesDirectory() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>Container name:</td><td><input type=\"text\" name=\"containerName\" value=\""+ settings.getContainerName() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>Use transformation:</td><td><select name=\"useTransformation\">" +
						"\n<option value=\"true\""+TR+">True</option>" +
						"\n<option value=\"false\""+FL+">False</option>" +
						"\n</select></td></tr>" +
						"\n<tr><td>XSLT path for PMML:</td><td><input type=\"text\" name=\"xsltPathPMML\" value=\""+ settings.getPmmlTransformationPath() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>XSLT path for BKEF:</td><td><input type=\"text\" name=\"xsltPathBKEF\" value=\""+ settings.getBkefTransformationPath() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>Temporary directory:</td><td><input type=\"text\" name=\"tempDir\" value=\""+ settings.getTemporaryDirectory() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td>Schema Path:</td><td><input type=\"text\" name=\"schemaPath\" value=\""+ settings.getValidationSchemaPath() +"\" size=\"100\"></td></tr>" +
						"\n<tr><td></td><td><input type=\"submit\" value=\"Save\"></td></tr>" +
						"\n</form>" +
						"\n</table>" +
						"\n</div></body></html>";
	}

}
