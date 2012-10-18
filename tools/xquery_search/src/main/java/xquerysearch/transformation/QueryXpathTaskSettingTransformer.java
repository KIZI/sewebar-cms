package xquerysearch.transformation;

import java.util.HashSet;
import java.util.Set;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.Coefficient;
import xquerysearch.domain.arbquery.DbaSetting;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.BBASetting;
import xquerysearch.domain.arbquery.tasksetting.DBASetting;

/**
 * Transformer used to transform query as object to XPath stored as String - for
 * searching in TaskSetting.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryXpathTaskSettingTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryXpathTaskSettingTransformer() {
	}

	/**
	 * Transforms {@link ArTsBuilderQuery} to XPath query.
	 * 
	 * @param query
	 * @return XPath query, <code>null</code> if error occurred
	 */
	public static String transformToXpath(ArTsBuilderQuery query, QuerySettings settings) {
		StringBuffer xpath = new StringBuffer();

		xpath.append("/PMML/AssociationRule[");

		transformNormal(query, xpath);
		xpath.append("]");

		System.out.println(xpath);
		return xpath.toString();
	}

	private static void transformNormal(ArTsBuilderQuery query, StringBuffer xpath) {
		String antecedentSetting = query.getArQuery().getAntecedentSetting();
		String consequentSetting = query.getArQuery().getConsequentSetting();
		String conditionSetting = query.getArQuery().getConditionSetting();

		Set<DBASetting> dbaSettings = query.getArQuery().getDbaSettings();
		Set<BBASetting> bbaSettings = query.getArQuery().getBbaSettings();

		if (antecedentSetting != null && antecedentSetting.isEmpty() == false) {
			xpath.append("count(AntecedentSetting/" + processCedent(antecedentSetting, dbaSettings, bbaSettings)
					+ ") > 0");
			if ((consequentSetting != null && consequentSetting.isEmpty() == false)
					|| (conditionSetting != null && conditionSetting.isEmpty() == false)) {
				xpath.append(" and ");
			}
		}
		if (consequentSetting != null && consequentSetting.isEmpty() == false) {
			xpath.append("count(ConsequentSetting/" + processCedent(consequentSetting, dbaSettings, bbaSettings)
					+ ") > 0");
			if (conditionSetting != null && conditionSetting.isEmpty() == false) {
				xpath.append(" and ");
			}
		}
		if (conditionSetting != null && conditionSetting.isEmpty() == false) {
			xpath.append("count(ConditionSetting/" + processCedent(conditionSetting, dbaSettings, bbaSettings)
					+ ") > 0");
		}
	}

	/**
	 * 
	 * @param currentId
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static String processCedent(String currentId, Set<DBASetting> dbaSettings,
			Set<BBASetting> bbaSettings) {
		Set<String> relatedBaRefs = new HashSet<String>();
		StringBuffer xpath = new StringBuffer();

		for (DBASetting dbaSetting : dbaSettings) {
			if (dbaSetting.getId().equals(currentId)) {
				for (String baRef : dbaSetting.getBaSettingRefs()) {
					relatedBaRefs.add(baRef);
					// dbaSettings.remove(dbaSetting);
				}
			}
		}

		if (relatedBaRefs.size() == 0) {
			for (BBASetting bbaSetting : bbaSettings) {
//				if (bbaSetting.getId().equals(currentId)) {
//
//					if (bbaSetting.getFieldRef() != null) {
//						xpath.append("/BBASetting[");
//					} else {
//						continue;
//					}
//
//					// TODO set value some other way
//
//					xpath.append("FieldRef = \"" + bbaSetting.getFieldRef().getValue() + "\" and (");
//
//					Coefficient coefficient = bbaSetting.getCoefficient();
//					if (coefficient.getCategories() != null) {
//						String connective = "and";
//						if (coefficient.getType().equals("At least one from listed")) {
//							connective = "or";
//						}
//						int i = 0;
//						for (String category : coefficient.getCategories()) {
//							if (i > 0) {
//								xpath.append(" " + connective + " ");
//							}
//							xpath.append("Coefficient = \"" + category + "\"");
//						}
//					}
//					xpath.append(")]");
//				}
			}
		}

		for (String baRef : relatedBaRefs) {
			if (relatedBaRefs.size() == 1) {
				xpath.append("/DBA");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
			} else if (relatedBaRefs.size() > 1) {
				xpath.append("[DBA");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
				xpath.append("]");
			}
		}

		return xpath.toString();
	}
}
