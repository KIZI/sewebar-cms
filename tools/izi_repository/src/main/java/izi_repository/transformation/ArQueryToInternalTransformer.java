package izi_repository.transformation;

import izi_repository.domain.ArQueryInternal;
import izi_repository.domain.arbquery.ArQuery;
import izi_repository.domain.arbquery.BbaSetting;
import izi_repository.domain.arbquery.DbaSetting;
import izi_repository.domain.arbquery.InterestMeasureSetting;
import izi_repository.domain.arbquery.InterestMeasureThreshold;
import izi_repository.domain.arbquery.datadescription.DataDescription;

import java.util.ArrayList;
import java.util.List;


/**
 * Transformer for transformation from {@link ArQuery} to
 * {@link ArQueryInternal}.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryToInternalTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ArQueryToInternalTransformer() {
	}

	/**
	 * Transforms {@link ArQuery} to {@link ArQueryInternal}.
	 * 
	 * @param arQuery
	 * @return
	 */
	public static ArQueryInternal transform(ArQuery arQuery, DataDescription dataDescription) {
		ArQueryInternal aqi = new ArQueryInternal();

		String antecedent = arQuery.getAntecedentSetting();
		String consequent = arQuery.getConsequentSetting();
		String condition = arQuery.getConditionSetting();

		List<DbaSetting> dbaSettings = arQuery.getDbaSettings();
		List<BbaSetting> bbaSettings = arQuery.getBbaSettings();

		aqi.setAntecedentBbaSettingList(getBbaSettingsForCedent(antecedent, dbaSettings, bbaSettings));
		aqi.setConsequentBbaSettingList(getBbaSettingsForCedent(consequent, dbaSettings, bbaSettings));
		aqi.setConditionBbaSettingList(getBbaSettingsForCedent(condition, dbaSettings, bbaSettings));
		
		aqi.setImThresholdList(getThresholds(arQuery.getInterestMeasureSetting()));
		
		aqi.setDataDescription(dataDescription);
		
		return aqi;
	}

	/**
	 * Goes through {@link DbaSetting}s and retrieves appropriate
	 * {@link BbaSetting}s. Uses string id as search key.
	 * 
	 * @param cedent
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static List<BbaSetting> getBbaSettingsForCedent(String cedent, List<DbaSetting> dbaSettings,
			List<BbaSetting> bbaSettings) {
		List<BbaSetting> ret = new ArrayList<BbaSetting>();

		List<DbaSetting> dbaList = new ArrayList<DbaSetting>();
		if (dbaSettings != null) {
			dbaList.addAll(dbaSettings);
		}

		if (cedent != null) {
			for (DbaSetting dbaSetting : dbaList) {
				if (dbaSetting.getId().equals(cedent)) {
					ret.addAll(getBbaSettingsForDbaSetting(dbaSetting.getBaSettingRefs(), dbaSettings,
							bbaSettings));
				}
			}
		}

		return ret;
	}

	/**
	 * Goes through {@link DbaSetting}s and retrieves appropriate
	 * {@link BbaSetting}s. Uses set of string ids as search key.
	 * 
	 * @param baRefs
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static List<BbaSetting> getBbaSettingsForDbaSetting(List<String> baRefs, List<DbaSetting> dbaSettings,
			List<BbaSetting> bbaSettings) {
		List<BbaSetting> ret = new ArrayList<BbaSetting>();

		List<DbaSetting> dbaList = new ArrayList<DbaSetting>();
		if (dbaSettings != null) {
			dbaList.addAll(dbaSettings);
		}

		List<BbaSetting> bbaList = new ArrayList<BbaSetting>();
		if (bbaSettings != null) {
			bbaList.addAll(bbaSettings);
		}

		if (baRefs != null) {
			for (String baRef : baRefs) {
				for (BbaSetting bbaSetting : bbaList) {
					if (bbaSetting.getId().equals(baRef)) {
						ret.add(bbaSetting);
					}
				}
				for (DbaSetting dbaSetting : dbaList) {
					if (dbaSetting.getId().equals(baRef)) {
						ret.addAll(getBbaSettingsForDbaSetting(dbaSetting.getBaSettingRefs(), dbaSettings,
								bbaSettings));
					}
				}
			}
		}
		return ret;
	}
	
	/**
	 * Used for retrieve {@link InterestMeasureThreshold}s from {@link InterestMeasureSetting}.
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
