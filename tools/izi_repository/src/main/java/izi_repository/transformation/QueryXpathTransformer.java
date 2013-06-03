package izi_repository.transformation;

import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.ArQuery;
import izi_repository.domain.arbquery.BbaSetting;
import izi_repository.domain.arbquery.Coefficient;
import izi_repository.domain.arbquery.DbaSetting;
import izi_repository.domain.arbquery.InterestMeasureThreshold;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.querysettings.QueryType;

import java.util.ArrayList;
import java.util.List;


/**
 * Transformer used to transform query as object to XPath stored as String - for
 * searching in Association Rules.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryXpathTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryXpathTransformer() {
	}

	/**
	 * Transforms {@link ArBuilderQuery} to XPath query. Return value is wrapped
	 * by <tt>AssociationRule[</tt> and <tt>]</tt> strings.
	 * 
	 * @param query
	 * @return XPath query, empty string if error occurred
	 */
	public static String transformToXpath(ArBuilderQuery query, QuerySettings settings) {
		if (query == null) {
			return "";
		}
		return "AssociationRule[" + transformToXpath(query.getArQuery(), settings) + "]";
	}

	/**
	 * Transforms {@link ArQuery} to XPath query. Return value is NOT wrapped by
	 * any string.
	 * 
	 * @param query
	 * @return XPath query, empty string if error occurred
	 */
	public static String transformToXpath(ArQuery query, QuerySettings settings) {
		if (query == null) {
			return "";
		}

		if (settings != null && settings.getType() != null && settings.getType().equals(QueryType.SHORTER.getText()) == true) {
			return transformShorter(query);
		} else {
			return transformNormal(query);
		}
	}

	private static String transformNormal(ArQuery query) {
		StringBuffer xpath = new StringBuffer();

		String antecedentSetting = query.getAntecedentSetting();
		String consequentSetting = query.getConsequentSetting();
		String conditionSetting = query.getConditionSetting();

		List<DbaSetting> dbaSettings = query.getDbaSettings();
		List<BbaSetting> bbaSettings = query.getBbaSettings();

		String connector = "";

		if (antecedentSetting != null && antecedentSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("count(Antecedent" + processCedent(antecedentSetting, dbaSettings, bbaSettings)
					+ ") > 0");
			connector = " and ";
		}
		if (consequentSetting != null && consequentSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("count(Consequent" + processCedent(consequentSetting, dbaSettings, bbaSettings)
					+ ") > 0");
			connector = " and ";
		}
		if (conditionSetting != null && conditionSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("count(Condition" + processCedent(conditionSetting, dbaSettings, bbaSettings)
					+ ") > 0");
			connector = " and ";
		}

		if (query.getInterestMeasureSetting() != null) {
			xpath.append(processImSetting(query.getInterestMeasureSetting().getImThresholds(), connector));
			connector = " and ";
		}

		return xpath.toString();
	}

	private static String transformShorter(ArQuery query) {
		StringBuffer xpath = new StringBuffer();

		String antecedentSetting = query.getAntecedentSetting();
		String consequentSetting = query.getConsequentSetting();
		String conditionSetting = query.getConditionSetting();

		List<DbaSetting> dbaSettings = query.getDbaSettings();
		List<BbaSetting> bbaSettings = query.getBbaSettings();

		String connector = "";

		if (antecedentSetting != null && antecedentSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("("
					+ processCedentShorter(antecedentSetting, dbaSettings, bbaSettings, "Antecedent", 0, 0)
					+ ")");
			connector = " and ";
		}
		if (consequentSetting != null && consequentSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("("
					+ processCedentShorter(consequentSetting, dbaSettings, bbaSettings, "Consequent", 0, 0)
					+ ")");
			connector = " and ";
		}
		if (conditionSetting != null && conditionSetting.isEmpty() == false) {
			xpath.append(connector);
			xpath.append("("
					+ processCedentShorter(conditionSetting, dbaSettings, bbaSettings, "Condition", 0, 0)
					+ ")");
			connector = " and ";
		}

		if (query.getInterestMeasureSetting() != null) {
			xpath.append(processImSetting(query.getInterestMeasureSetting().getImThresholds(), connector));
			connector = " and ";
		}

		return xpath.toString();
	}

	private static String processCedentShorter(String currentId, List<DbaSetting> dbaSettings, List<BbaSetting> bbaSettings, String cedentName, int step, int queryType) {
		List<String> relatedBaRefs = new ArrayList<String>();
		StringBuffer xpath = new StringBuffer();

		for (DbaSetting dbaSetting : dbaSettings) {
			if (dbaSetting.getId().equals(currentId)) {
				for (String baRef : dbaSetting.getBaSettingRefs()) {
					relatedBaRefs.add(baRef);
				}
			}
		}

		if (relatedBaRefs.size() == 0) {
			xpath = processBbas(currentId, bbaSettings, xpath, queryType);
		}

		StringBuffer noOthersCondition = new StringBuffer();
		noOthersCondition.append(" and count(" + cedentName + "//DBA/BBA[");

		int loopCount = 0;
		for (String baRef : relatedBaRefs) {
			if (step == 0) {
				if (loopCount > 0) {
					xpath.append(" or ");
					noOthersCondition.append(" and ");
				}
				xpath.append("(");
				xpath.append("count(" + cedentName);
				xpath.append("/DBA");
				xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, step + 1, 1));
				xpath.append(") > 0");
				xpath.append(" and count(" + cedentName);
				xpath.append("/DBA");
				xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, step + 1, 2));
				xpath.append(") = 0");
				xpath.append(")");

			} else {
				if (queryType < 3) {
					xpath.append("/DBA");
				}
				xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, step + 1,
						queryType));
			}
			noOthersCondition.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName,
					step + 1, 3));
			loopCount++;
		}
		noOthersCondition.append("]) = 0");
		if (step == 0) {
			xpath.append(noOthersCondition);
		}

		return xpath.toString();
	}

	private static StringBuffer processBbas(String currentId, List<BbaSetting> bbaSettings, StringBuffer xpath, int queryType) {
		for (BbaSetting bbaSetting : bbaSettings) {
			if (bbaSetting.getId().equals(currentId)) {

				if (bbaSetting.getFieldRef() != null) {
					String dictionary = bbaSetting.getFieldRef().getDictionary();
					if (queryType < 3) {
						xpath.append("/BBA/" + dictionary + "[");
					} else {
						xpath.append(dictionary + "/");
					}
				} else {
					continue;
				}

				// TODO set value some other way

				if (queryType < 3) {
					xpath.append("FieldName = \"" + bbaSetting.getFieldRef().getValue() + "\" and (");

					Coefficient coefficient = bbaSetting.getCoefficient();
					if (coefficient.getCategories() != null) {
						String connective = "and";
						if (coefficient.getType().equals("At least one from listed") && queryType != 2) {
							connective = "or";
						}

						String categorySign = "=";

						if (coefficient.getType().equals("None from listed") || queryType == 2) {
							categorySign = "!=";
						}

						int i = 0;
						for (String category : coefficient.getCategories()) {
							if (i > 0) {
								xpath.append(" " + connective + " ");
							}
							xpath.append("CatName " + categorySign + " \"" + category + "\"");
							i++;
						}
					}
					xpath.append(")");
					xpath.append("]");
				} else {
					xpath.append("FieldName != \"" + bbaSetting.getFieldRef().getValue() + "\"");
				}
			}
		}
		return xpath;
	}

	/**
	 * 
	 * @param currentId
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static String processCedent(String currentId, List<DbaSetting> dbaSettings, List<BbaSetting> bbaSettings) {
		List<String> relatedBaRefs = new ArrayList<String>();
		StringBuffer xpath = new StringBuffer();

		for (DbaSetting dbaSetting : dbaSettings) {
			if (dbaSetting.getId().equals(currentId)) {
				for (String baRef : dbaSetting.getBaSettingRefs()) {
					relatedBaRefs.add(baRef);
				}
			}
		}

		if (relatedBaRefs.size() == 0) {
			processBbas(currentId, bbaSettings, xpath, 1);
		}

		int loopCount = 0;
		for (String baRef : relatedBaRefs) {
			if (relatedBaRefs.size() == 1) {
				xpath.append("/DBA");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
			} else if (relatedBaRefs.size() > 1) {
				if (loopCount == 0) {
					xpath.append("[");
				}
				if (loopCount > 0) {
					xpath.append(" and ");
				}
				xpath.append("DBA");
				xpath.append(processCedent(baRef, dbaSettings, bbaSettings));
				if (loopCount == (relatedBaRefs.size() - 1)) {
					xpath.append("]");
				}
				loopCount++;
			}
		}

		return xpath.toString();
	}

	private static String processImSetting(List<InterestMeasureThreshold> imThresholds, String mainConnector) {
		if (imThresholds == null) {
			return "";
		}

		StringBuffer ret = new StringBuffer();

		String connector = "";
		for (InterestMeasureThreshold threshold : imThresholds) {
			if (threshold.getInterestMeasure() != null
					&& threshold.getInterestMeasure().equals("Any Interest Measure") == false) {
				ret.append(connector);
				ret.append("count(IMValue");
				ret.append("[@name = \"" + threshold.getInterestMeasure() + "\"");
				ret.append(" and . " + getThresholdCompareType(threshold.getCompareType())
						+ threshold.getSignificanceLevel());
				ret.append("]) > 0");
				connector = " and ";
			}
		}

		if (ret.length() > 0) {
			return mainConnector + "(" + ret.toString() + ")";
		}
		return "";
	}

	private static String getThresholdCompareType(String compareType) {
		if (compareType != null) {
			if (compareType.equals("Greater than or equal")) {
				return ">=";
			} else if (compareType.equals("Less than or equal")) {
				return "<=";
			} else if (compareType.equals("Greater than")) {
				return ">";
			} else if (compareType.equals("Less than")) {
				return "<";
			} else if (compareType.equals("Equal")) {
				return "=";
			}

		}
		return ">=";
	}
}
