package izi_repository.domain.result.tasksetting;

/**
 * Domain object representing Extension [@name="LISp-Miner"] from TaskSetting
 * from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Extension {

	private String name;
	private String taskGroup;
	private String taskState;
	private Integer numberOfVerifications;
	private String taskStartTime;
	private String taskDuration;
	private String ftMissingsType;
	private String ftTaskParamProlong100aFlag;
	private String ftTaskParamProlong100sFlag;
	private String ftTaskParamPrimeCheckMinLen;
	private String ftTaskParamPrimeCheck;
	private String ftTaskParamIncludeSymetricFlag;
	private Integer hypothesesCountMax;
	private String taskNotice;

	/**
	 * @return the name
	 */
	public String getName() {
		return name;
	}

	/**
	 * @param name
	 *            the name to set
	 */
	public void setName(String name) {
		this.name = name;
	}

	/**
	 * @return the taskGroup
	 */
	public String getTaskGroup() {
		return taskGroup;
	}

	/**
	 * @param taskGroup
	 *            the taskGroup to set
	 */
	public void setTaskGroup(String taskGroup) {
		this.taskGroup = taskGroup;
	}

	/**
	 * @return the taskState
	 */
	public String getTaskState() {
		return taskState;
	}

	/**
	 * @param taskState
	 *            the taskState to set
	 */
	public void setTaskState(String taskState) {
		this.taskState = taskState;
	}

	/**
	 * @return the numberOfVerifications
	 */
	public Integer getNumberOfVerifications() {
		return numberOfVerifications;
	}

	/**
	 * @param numberOfVerifications
	 *            the numberOfVerifications to set
	 */
	public void setNumberOfVerifications(Integer numberOfVerifications) {
		this.numberOfVerifications = numberOfVerifications;
	}

	/**
	 * @return the taskStartTime
	 */
	public String getTaskStartTime() {
		return taskStartTime;
	}

	/**
	 * @param taskStartTime
	 *            the taskStartTime to set
	 */
	public void setTaskStartTime(String taskStartTime) {
		this.taskStartTime = taskStartTime;
	}

	/**
	 * @return the taskDuration
	 */
	public String getTaskDuration() {
		return taskDuration;
	}

	/**
	 * @param taskDuration
	 *            the taskDuration to set
	 */
	public void setTaskDuration(String taskDuration) {
		this.taskDuration = taskDuration;
	}

	/**
	 * @return the ftMissingsType
	 */
	public String getFtMissingsType() {
		return ftMissingsType;
	}

	/**
	 * @param ftMissingsType
	 *            the ftMissingsType to set
	 */
	public void setFtMissingsType(String ftMissingsType) {
		this.ftMissingsType = ftMissingsType;
	}

	/**
	 * @return the ftTaskParamProlong100aFlag
	 */
	public String getFtTaskParamProlong100aFlag() {
		return ftTaskParamProlong100aFlag;
	}

	/**
	 * @param ftTaskParamProlong100aFlag
	 *            the ftTaskParamProlong100aFlag to set
	 */
	public void setFtTaskParamProlong100aFlag(String ftTaskParamProlong100aFlag) {
		this.ftTaskParamProlong100aFlag = ftTaskParamProlong100aFlag;
	}

	/**
	 * @return the ftTaskParamProlong100sFlag
	 */
	public String getFtTaskParamProlong100sFlag() {
		return ftTaskParamProlong100sFlag;
	}

	/**
	 * @param ftTaskParamProlong100sFlag
	 *            the ftTaskParamProlong100sFlag to set
	 */
	public void setFtTaskParamProlong100sFlag(String ftTaskParamProlong100sFlag) {
		this.ftTaskParamProlong100sFlag = ftTaskParamProlong100sFlag;
	}

	/**
	 * @return the ftTaskParamPrimeCheckMinLen
	 */
	public String getFtTaskParamPrimeCheckMinLen() {
		return ftTaskParamPrimeCheckMinLen;
	}

	/**
	 * @param ftTaskParamPrimeCheckMinLen
	 *            the ftTaskParamPrimeCheckMinLen to set
	 */
	public void setFtTaskParamPrimeCheckMinLen(String ftTaskParamPrimeCheckMinLen) {
		this.ftTaskParamPrimeCheckMinLen = ftTaskParamPrimeCheckMinLen;
	}

	/**
	 * @return the ftTaskParamPrimeCheck
	 */
	public String getFtTaskParamPrimeCheck() {
		return ftTaskParamPrimeCheck;
	}

	/**
	 * @param ftTaskParamPrimeCheck
	 *            the ftTaskParamPrimeCheck to set
	 */
	public void setFtTaskParamPrimeCheck(String ftTaskParamPrimeCheck) {
		this.ftTaskParamPrimeCheck = ftTaskParamPrimeCheck;
	}

	/**
	 * @return the ftTaskParamIncludeSymetricFlag
	 */
	public String getFtTaskParamIncludeSymetricFlag() {
		return ftTaskParamIncludeSymetricFlag;
	}

	/**
	 * @param ftTaskParamIncludeSymetricFlag
	 *            the ftTaskParamIncludeSymetricFlag to set
	 */
	public void setFtTaskParamIncludeSymetricFlag(String ftTaskParamIncludeSymetricFlag) {
		this.ftTaskParamIncludeSymetricFlag = ftTaskParamIncludeSymetricFlag;
	}

	/**
	 * @return the hypothesesCountMax
	 */
	public Integer getHypothesesCountMax() {
		return hypothesesCountMax;
	}

	/**
	 * @param hypothesesCountMax
	 *            the hypothesesCountMax to set
	 */
	public void setHypothesesCountMax(Integer hypothesesCountMax) {
		this.hypothesesCountMax = hypothesesCountMax;
	}

	/**
	 * @return the taskNotice
	 */
	public String getTaskNotice() {
		return taskNotice;
	}

	/**
	 * @param taskNotice
	 *            the taskNotice to set
	 */
	public void setTaskNotice(String taskNotice) {
		this.taskNotice = taskNotice;
	}

}
