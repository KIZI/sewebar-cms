package izi_repository.analysis;

import izi_repository.domain.TaskSettingInternal;
import izi_repository.domain.analysis.TaskSettingAnalysisOutput;
import izi_repository.domain.result.tasksetting.TaskSetting;

/**
 * Analyzer for {@link TaskSettingInternal}.
 * 
 * @author Tomas Marek
 *
 */
public class TaskSettingAnalyzer {
	
	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private TaskSettingAnalyzer() {
	}
	
	/**
	 * Analyzes {@link TaskSetting} represented by
	 * {@link TaskSettingInternal}.
	 * 
	 * @param tsi
	 * @return {@link TaskSettingAnalysisOutput} describing {@link TaskSetting}
	 */
	public static TaskSettingAnalysisOutput analyze(TaskSettingInternal tsi) {
		TaskSettingAnalysisOutput output = new TaskSettingAnalysisOutput();

		output.setAntecedentBbaCount(tsi.getAntecedentBbaSettings().size());
		output.setConsequentBbaCount(tsi.getConsequentBbaSettings().size());
		output.setConditionBbaCount(tsi.getConditionBbaSettings().size());

		return output;
	}

}
