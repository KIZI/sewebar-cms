package xquerysearch.transformation;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.ArTsQuery;
import xquerysearch.domain.arbquery.tasksetting.BBASetting;
import xquerysearch.domain.arbquery.tasksetting.Coefficient;
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
	 * Return is wrapped by <tt>TaskSetting[</tt> and <tt>]</tt> strings.
	 * 
	 * @param query
	 * @param settings
	 * @return XPath query, empty string if error occurred
	 */
	public static String transformToXpath(ArTsBuilderQuery query, QuerySettings settings) {
		if (query == null) {
			return "";
		}
		return "TaskSetting[" + transformToXpath(query.getArTsQuery(), settings) + "]";
	}

	/**
	 * Transforms {@link ArTsQuery} to XPath query.
	 * Return is NOT wrapped by any strings.
	 * 
	 * @param query
	 * @param settings
	 * @return XPath query, empty string if error occurred
	 */
	public static String transformToXpath(ArTsQuery query, QuerySettings settings) {
		if (query != null) {
			return transformNormal(query);
		}
		return "";
	}
	
	private static String transformNormal(ArTsQuery query) {
		StringBuffer xpath = new StringBuffer();
		
		String antecedentSetting = query.getAntecedentSetting();
		String consequentSetting = query.getConsequentSetting();
		String conditionSetting = query.getConditionSetting();

		List<DBASetting> dbaSettings = query.getDbaSettings();
		List<BBASetting> bbaSettings = query.getBbaSettings();

		if (antecedentSetting != null && antecedentSetting.isEmpty() == false) {
			xpath.append("count(AntecedentSetting/"
					+ processCedent(antecedentSetting, dbaSettings, bbaSettings) + ") > 0");
			if ((consequentSetting != null && consequentSetting.isEmpty() == false)
					|| (conditionSetting != null && conditionSetting.isEmpty() == false)) {
				xpath.append(" and ");
			}
		}
		if (consequentSetting != null && consequentSetting.isEmpty() == false) {
			xpath.append("count(ConsequentSetting/"
					+ processCedent(consequentSetting, dbaSettings, bbaSettings) + ") > 0");
			if (conditionSetting != null && conditionSetting.isEmpty() == false) {
				xpath.append(" and ");
			}
		}
		if (conditionSetting != null && conditionSetting.isEmpty() == false) {
			xpath.append("count(ConditionSetting/"
					+ processCedent(conditionSetting, dbaSettings, bbaSettings) + ") > 0");
		}
		
		return xpath.toString();
	}

	/**
	 * 
	 * @param currentId
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static String processCedent(String currentId, List<DBASetting> dbaSettings, List<BBASetting> bbaSettings) {
		List<String> relatedBaRefs = new ArrayList<String>();
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
				if (bbaSetting.getId().equals(currentId)) {

					if (bbaSetting.getFieldRef() != null) {
						xpath.append("/BBASetting[");
					} else {
						continue;
					}

					// TODO set value some other way

					xpath.append("FieldRef = \"" + bbaSetting.getFieldRef() + "\" and Coefficient[");

					Coefficient coefficient = bbaSetting.getCoefficient();
					if (coefficient != null) {
						xpath.append(processCoefficient(coefficient));
					}
					xpath.append("]]");
				}
			}
		}

		for (String baRef : relatedBaRefs) {
			if (relatedBaRefs.size() == 1) {
				xpath.append("/DBASetting");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
			} else if (relatedBaRefs.size() > 1) {
				xpath.append("[DBASetting");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
				xpath.append("]");
			}
		}

		return xpath.toString();
	}

	private static String processCoefficient(Coefficient coefficient) {
		String ret = "";
		String connective = "";
		String type = coefficient.getType();
		if (type != null) {
			ret += "Type = \"" + coefficient.getType() + "\"";
			connective = " and ";
		}
		Integer minimalLength = coefficient.getMinimalLength();
		if (minimalLength != null) {
			ret += connective + "MinimalLength=" + minimalLength;
			connective = " and ";
		}
		Integer maximalLength = coefficient.getMaximalLength();
		if (maximalLength != null) {
			ret += connective + "MaximalLength=" + maximalLength;
			connective = " and ";
		}
		String category = coefficient.getCategory();
		if (category != null) {
			ret += connective + "Category = \"" + category + "\"";
		}

		return ret;
	}
}
