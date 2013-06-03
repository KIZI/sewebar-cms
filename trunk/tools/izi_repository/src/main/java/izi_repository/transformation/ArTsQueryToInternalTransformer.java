package izi_repository.transformation;

import izi_repository.domain.ArTsQueryInternal;
import izi_repository.domain.arbquery.InterestMeasureSetting;
import izi_repository.domain.arbquery.InterestMeasureThreshold;
import izi_repository.domain.arbquery.tasksetting.ArTsQuery;
import izi_repository.domain.arbquery.tasksetting.BBASetting;
import izi_repository.domain.arbquery.tasksetting.DBASetting;

import java.util.ArrayList;
import java.util.List;


/**
 * Transformer for transformation from {@link ArTsQuery} to
 * {@link ArTsQueryInternal}.
 * 
 * @author Tomas
 * 
 */
public class ArTsQueryToInternalTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ArTsQueryToInternalTransformer() {
	}

	/**
	 * Transforms {@link ArTsQuery} to {@link ArTsQueryInternal}.
	 * 
	 * @param arTsQuery
	 * @return
	 */
	public static ArTsQueryInternal transform(ArTsQuery arTsQuery) {
		ArTsQueryInternal aqti = new ArTsQueryInternal();

		String antecedent = arTsQuery.getAntecedentSetting();
		String consequent = arTsQuery.getConsequentSetting();
		String condition = arTsQuery.getConditionSetting();

		List<DBASetting> dbaSettings = arTsQuery.getDbaSettings();
		List<BBASetting> bbaSettings = arTsQuery.getBbaSettings();

		aqti.setAntecedentBbaSettings(getBbaSettingsForCedent(antecedent, dbaSettings, bbaSettings));
		aqti.setConsequentBbaSettings(getBbaSettingsForCedent(consequent, dbaSettings, bbaSettings));
		aqti.setConditionBbaSettings(getBbaSettingsForCedent(condition, dbaSettings, bbaSettings));

		aqti.setImThresholdList(getThresholds(arTsQuery.getImSetting()));

		return aqti;
	}

	/**
	 * Goes through {@link DBASetting}s and retrieves appropriate
	 * {@link BBASetting}s. Uses string id as search key.
	 * 
	 * @param cedent
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static List<BBASetting> getBbaSettingsForCedent(String cedent, List<DBASetting> dbaSettings, List<BBASetting> bbaSettings) {
		List<BBASetting> ret = new ArrayList<BBASetting>();

		List<DBASetting> dbaList = new ArrayList<DBASetting>();
		if (dbaSettings != null) {
			dbaList.addAll(dbaSettings);
		}

		if (cedent != null) {
			for (DBASetting dbaSetting : dbaList) {
				if (dbaSetting.getId().equals(cedent)) {
					ret.addAll(getBBASettingsForDBASetting(dbaSetting.getBaSettingRefs(), dbaSettings,
							bbaSettings));
				}
			}
		}

		return ret;
	}

	/**
	 * Goes through {@link DBASetting}s and retrieves appropriate
	 * {@link BBASetting}s. Uses set of string ids as search key.
	 * 
	 * @param baRefs
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static List<BBASetting> getBBASettingsForDBASetting(List<String> baRefs, List<DBASetting> dbaSettings, List<BBASetting> bbaSettings) {
		List<BBASetting> ret = new ArrayList<BBASetting>();

		List<DBASetting> dbaList = new ArrayList<DBASetting>();
		if (dbaSettings != null) {
			dbaList.addAll(dbaSettings);
		}

		List<BBASetting> bbaList = new ArrayList<BBASetting>();
		if (bbaSettings != null) {
			bbaList.addAll(bbaSettings);
		}

		if (baRefs != null) {
			for (String baRef : baRefs) {
				for (BBASetting bbaSetting : bbaList) {
					if (bbaSetting.getId().equals(baRef)) {
						ret.add(bbaSetting);
					}
				}
				for (DBASetting dbaSetting : dbaList) {
					if (dbaSetting.getId().equals(baRef)) {
						ret.addAll(getBBASettingsForDBASetting(dbaSetting.getBaSettingRefs(), dbaSettings,
								bbaSettings));
					}
				}
			}
		}
		return ret;
	}

	/**
	 * Used for retrieve {@link InterestMeasureThreshold}s from
	 * {@link InterestMeasureSetting}.
	 * 
	 * @param imSettings
	 * @return
	 */
	private static List<InterestMeasureThreshold> getThresholds(InterestMeasureSetting imSetting) {
		List<InterestMeasureThreshold> ret = new ArrayList<InterestMeasureThreshold>();
		if (imSetting != null && imSetting.getImThresholds() != null) {
			ret.addAll(imSetting.getImThresholds());
		}
		return ret;
	}
}
