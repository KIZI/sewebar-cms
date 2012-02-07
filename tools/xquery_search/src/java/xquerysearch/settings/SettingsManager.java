package xquerysearch.settings;

import java.io.File;
import java.io.IOException;

import javax.servlet.http.HttpServletRequest;

/**
 * This class provides management for application settings 
 * @author Tomas Marek
 */

public class SettingsManager {
	private String environmentDirectory;
	private String queriesDirectory;
	private String containerName;
	private boolean useTransformation;
	private String pmmlTransformationPath;
	private String bkefTransformationPath;
	private String temporaryDirectory;
	private String validationSchemaPath;
	
	/**
	 * 
	 * @return environment directory
	 */
	public String getEnvironmentDirectory() {
		return environmentDirectory;
	}
	
	/**
	 * Sets the environment directory
	 * @param environmentDirectory environment directory path
	 */
	public void setEnvironmentDirectory(String environmentDirectory) {
		this.environmentDirectory = environmentDirectory;
	}
	
	/**
	 * 
	 * @return directory for saving queries
	 */
	public String getQueriesDirectory() {
		return queriesDirectory;
	}
	
	/**
	 * Sets the directory for queries
	 * @param queriesDirectory query directory path 
	 */
	public void setQueriesDirectory(String queriesDirectory) {
		this.queriesDirectory = queriesDirectory;
	}
	
	/**
	 * 
	 * @return name of the container for storing documents (Berkeley XML DB container) 
	 */
	public String getContainerName() {
		return containerName;
	}
	
	/**
	 * Sets the name of Berkeley XML DB container where the documents are stored
	 * @param containerName name of container
	 */
	public void setContainerName(String containerName) {
		this.containerName = containerName;
	}
	
	/**
	 * 
	 * @return <code>TRUE</code> if transformation should be used 
	 */
	public boolean isUseTransformation() {
		return useTransformation;
	}
	
	/**
	 * Set <code>TRUE</code> when transformation should be used
	 * @param useTransformation
	 */
	public void setUseTransformation(boolean useTransformation) {
		this.useTransformation = useTransformation;
	}
	
	/**
	 * 
	 * @return path of the PMML document transformation (PMML -> XML DB inner storage format)
	 */
	public String getPmmlTransformationPath() {
		return pmmlTransformationPath;
	}
	
	/**
	 * PMML -> XML DB inner storage format 
	 * @param pmmlTransformationPath path of PMML transformation
	 */
	public void setPmmlTransformationPath(String pmmlTransformationPath) {
		this.pmmlTransformationPath = pmmlTransformationPath;
	}
	
	/**
	 * 
	 * @return path of the BKEF document transformation (BKEF -> XML DB inner storage format)
	 */
	public String getBkefTransformationPath() {
		return bkefTransformationPath;
	}
	
	/**
	 * BKEF -> XML DB inner storage format 
	 * @param pmmlTransformationPath path of BKEF transformation
	 */
	public void setBkefTransformationPath(String bkefTransformationPath) {
		this.bkefTransformationPath = bkefTransformationPath;
	}
	
	/**
	 * 
	 * @return temporary directory path
	 */
	public String getTemporaryDirectory() {
		return temporaryDirectory;
	}
	
	/**
	 * Set temporary directory path
	 * @param temporaryDirectory
	 */
	public void setTemporaryDirectory(String temporaryDirectory) {
		this.temporaryDirectory = temporaryDirectory;
	}
	
	/**
	 * 
	 * @return validation schema path (validation schema for PMML)
	 */
	public String getValidationSchemaPath() {
		return validationSchemaPath;
	}
	
	/**
	 * Set the PMML validation schema
	 * @param validationSchemaPath path to the schema
	 */
	public void setValidationSchemaPath(String validationSchemaPath) {
		this.validationSchemaPath = validationSchemaPath;
	}
	
	
	/**
	 * Metoda pro nacteni dat z html formulare ze stranky s nastavenim a odeslani nastaveni k zapisu do souboru
	 * @param sr instance tridy XMLSettingsReader
	 * @param settingsFile soubor s nastavenim
	 * @param request prijaty http request
	 * @throws IOException 
	 */
	public SettingsManager changeSettings(File settingsFile, HttpServletRequest request) throws IOException{
		this.setEnvironmentDirectory(request.getParameter("envDir"));
		this.setQueriesDirectory(request.getParameter("queryDir"));
		this.setContainerName(request.getParameter("containerName"));
		this.setUseTransformation(Boolean.valueOf(request.getParameter("useTransformation")));
		this.setPmmlTransformationPath(request.getParameter("xsltPathPMML"));
		this.setBkefTransformationPath(request.getParameter("xsltPathBKEF"));
		this.setTemporaryDirectory(request.getParameter("tempDir"));
		this.setValidationSchemaPath(request.getParameter("schemaPath"));
		SettingsUtils.writeSettings(settingsFile, this);
		return this;
	}

}
