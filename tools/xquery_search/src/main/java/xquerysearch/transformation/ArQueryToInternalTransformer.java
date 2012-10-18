package xquerysearch.transformation;

import java.util.ArrayList;
import java.util.List;
import java.util.Set;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.DbaSetting;
import xquerysearch.domain.arbquery.InterestMeasureSetting;
import xquerysearch.domain.arbquery.InterestMeasureThreshold;

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
	public static ArQueryInternal transform(ArQuery arQuery) {
		ArQueryInternal aqi = new ArQueryInternal();

		String antecedent = arQuery.getAntecedentSetting();
		String consequent = arQuery.getConsequentSetting();
		String condition = arQuery.getConditionSetting();

		Set<DbaSetting> dbaSettings = arQuery.getDbaSettings();
		Set<BbaSetting> bbaSettings = arQuery.getBbaSettings();

		aqi.setAntecedentBbaSettingList(getBbaSettingsForCedent(antecedent, dbaSettings, bbaSettings));
		aqi.setConsequentBbaSettingList(getBbaSettingsForCedent(consequent, dbaSettings, bbaSettings));
		aqi.setConditionBbaSettingList(getBbaSettingsForCedent(condition, dbaSettings, bbaSettings));
		
		aqi.setImThresholdList(getThresholds(arQuery.getInterestMeasureSetting()));
		
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
	private static List<BbaSetting> getBbaSettingsForCedent(String cedent, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings) {
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
	private static List<BbaSetting> getBbaSettingsForDbaSetting(Set<String> baRefs, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings) {
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
	 * Used for retrive {@link InterestMeasureThreshold}s from {@link InterestMeasureSetting}.
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
