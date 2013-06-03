package xquerysearch.transformation;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.TaskSettingInternal;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.tasksetting.BBASetting;
import xquerysearch.domain.result.tasksetting.CedentSetting;
import xquerysearch.domain.result.tasksetting.DBASetting;
import xquerysearch.domain.result.tasksetting.InterestMeasureThreshold;
import xquerysearch.domain.result.tasksetting.TaskSetting;

/**
 * Transformer for transformation from {@link TaskSetting} to
 * {@link TaskSettingInternal}.
 * 
 * @author Tomas Marek
 *
 */
public class TaskSettingToInternalTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private TaskSettingToInternalTransformer() {
	}
	
	/**
	 * Transforms {@link TaskSetting} to {@link TaskSettingInternal}.
	 * 
	 * @param rule
	 * @return
	 */
	public static TaskSettingInternal transform(TaskSetting taskSetting) {
		TaskSettingInternal tsi = new TaskSettingInternal();

		CedentSetting antecedentSetting = taskSetting.getAntecedentSetting();
		CedentSetting consequentSetting = taskSetting.getConsequentSetting();
		CedentSetting conditionSetting = taskSetting.getConditionSetting();

		tsi.setAntecedentBbaSettings(new ArrayList<BBASetting>(getBbaSettingsFromCedentSetting(antecedentSetting)));
		tsi.setConsequentBbaSettings(new ArrayList<BBASetting>(getBbaSettingsFromCedentSetting(consequentSetting)));
		tsi.setConditionBbaSettings(new ArrayList<BBASetting>(getBbaSettingsFromCedentSetting(conditionSetting)));

		if (taskSetting.getImSetting() != null) {
			tsi.setImThresholdList(new ArrayList<InterestMeasureThreshold>(taskSetting.getImSetting().getImThresholds()));
		} else {
			tsi.setImThresholdList(new ArrayList<InterestMeasureThreshold>());
		}

		return tsi;
	}
	
	/**
	 * Retrieves all {@link BBASetting}s from given {@link Result}.
	 * 
	 * @param result
	 * @return
	 */
	public static List<BBASetting> getBbaSettingsFromResult(Result result) {
		if (result == null) {
			return null;
		}

		List<BBASetting> ret = new ArrayList<BBASetting>();

		if (result.getTaskSetting() != null) {
			ret.addAll(getBbaSettingsFromCedentSetting(result.getTaskSetting().getAntecedentSetting()));
			ret.addAll(getBbaSettingsFromCedentSetting(result.getTaskSetting().getConsequentSetting()));
			ret.addAll(getBbaSettingsFromCedentSetting(result.getTaskSetting().getConditionSetting()));
		}

		return ret;
	}

	/**
	 * Goes through cedent's {@link DBASetting}s and retrieves their {@link BBASetting}s.
	 * 
	 * @param cedentSetting
	 * @return
	 */
	public static List<BBASetting> getBbaSettingsFromCedentSetting(CedentSetting cedentSetting) {
		List<BBASetting> ret = new ArrayList<BBASetting>();
		if (cedentSetting != null) {
			List<DBASetting> dbaSettings = cedentSetting.getDbaSettings();
			if (dbaSettings != null) {
				for (DBASetting dbaSetting : dbaSettings) {
					ret.addAll(getBbaSettingsFromDbaSetting(dbaSetting));
				}
			}
		}
		return ret;
	}

	/**
	 * Goes through {@link DBASetting}s and retrieves their {@link BBASetting}s.
	 * 
	 * @param dba
	 * @return
	 */
	public static List<BBASetting> getBbaSettingsFromDbaSetting(DBASetting dbaSetting) {
		List<BBASetting> ret = new ArrayList<BBASetting>();
		if (dbaSetting != null) {
			List<BBASetting> bbaSettings = dbaSetting.getBbaSettings();
			List<DBASetting> dbaSettings = dbaSetting.getDbaSettings();

			if (bbaSettings != null) {
				for (BBASetting bbaSetting : bbaSettings) {
					ret.add(bbaSetting);
				}
			}

			if (dbaSettings != null) {
				for (DBASetting dbaSettingOfDbaSetting : dbaSettings) {
					ret.addAll(getBbaSettingsFromDbaSetting(dbaSettingOfDbaSetting));
				}
			}
		}
		return ret;
	}
}
