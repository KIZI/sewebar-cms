package xquerysearch.analysis;

import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.DbaSetting;

/**
 * Analyzes {@link ArQuery}.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryAnalyzer {

	/**
	 * TODO documentation
	 * 
	 * @param query
	 * @return
	 */
	public static Map<String, Integer> analyze(ArBuilderQuery query) {
		Map<String, Integer> ret = new HashMap<String, Integer>();
		ArQuery arQuery = query.getArQuery();
		if (arQuery == null) {
			return ret;
		}

		Set<DbaSetting> dbaSettings = arQuery.getDbaSettings().getDbaSettings();
		Set<BbaSetting> bbaSettings = arQuery.getBbaSettings().getBbaSettings();

		ret.put("antecedentBbaCount", getBbaCount(arQuery.getAntecedentSetting(), dbaSettings, bbaSettings));
		ret.put("consequentBbaCount", getBbaCount(arQuery.getConsequentSetting(), dbaSettings, bbaSettings));
		ret.put("conditionBbaCount", getBbaCount(arQuery.getConditionSetting(), dbaSettings, bbaSettings));
		return ret;
	}

	/**
	 * TODO documentation
	 * 
	 * @param cedentSetting
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static Integer getBbaCount(String cedentSetting, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings) {

		if (cedentSetting == null || cedentSetting.isEmpty()) {
			return null;
		}

		int bbaCount = 0;
		Set<BbaSetting> bbaSettingsForCedent = getBbaSettingsForCedent(cedentSetting, dbaSettings,
				bbaSettings);
		if (bbaSettingsForCedent != null) {
			bbaCount += bbaSettingsForCedent.size();
		}

		return bbaCount;
	}

	/**
	 * TODO documentation
	 * 
	 * @param id
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static Set<BbaSetting> getBbaSettingsForCedent(String id, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings) {

		if (id == null || id.isEmpty()) {
			return null;
		}

		Set<BbaSetting> ret = new HashSet<BbaSetting>();
		
		for (DbaSetting dbaSetting : dbaSettings) {
			if (dbaSetting.getId().equals(id)) {
				for (String baRef : dbaSetting.getBaSettingRefs()) {
					ret.addAll(getBbaSettingsForCedent(baRef, dbaSettings, bbaSettings));
				}
			}
		}
		
		for (BbaSetting bbaSetting : bbaSettings) {
			if (bbaSetting.getId().equals(id)) {
				ret.add(bbaSetting);
			}
		}

		return ret;
	}
}
