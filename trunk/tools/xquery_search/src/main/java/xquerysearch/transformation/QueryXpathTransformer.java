package xquerysearch.transformation;

import java.util.HashSet;
import java.util.Set;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.Coefficient;
import xquerysearch.domain.arbquery.DbaSetting;

/**
 * Transformer used to transform query as object to XPath stored as String.
 * 
 * @author Tomas Marek
 * 
 */
public class QueryXpathTransformer {

	/**
	 * Temporary - get same or shorter association rule than query
	 */
	private static final boolean SHORTER_QUERYING_TYPE = true;

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryXpathTransformer() {
	}

	/**
	 * Transforms {@link ArBuilderQuery} to XPath query.
	 * 
	 * @param query
	 * @return XPath query, <code>null</code> if error occurred
	 */
	public static String transformToXpath(ArBuilderQuery query, String containerName) {
		String antecedentSetting = query.getArQuery().getAntecedentSetting();
		String consequentSetting = query.getArQuery().getConsequentSetting();
		String conditionSetting = query.getArQuery().getConditionSetting();
		String xpath = "";

		Set<DbaSetting> dbaSettings = query.getArQuery().getDbaSettings().getDbaSettings();
		Set<BbaSetting> bbaSettings = query.getArQuery().getBbaSettings().getBbaSettings();

		if (antecedentSetting != null && antecedentSetting.isEmpty() == false) {
			if (SHORTER_QUERYING_TYPE) {
				xpath += "("
						+ processCedentShorter(antecedentSetting, dbaSettings, bbaSettings, "Antecedent", 0,
								0) + ")";
			}
			if ((consequentSetting != null && consequentSetting.isEmpty() == false)
					|| (conditionSetting != null && conditionSetting.isEmpty() == false)) {
				xpath += " and ";
			}
		}
		if (consequentSetting != null && consequentSetting.isEmpty() == false) {
			if (SHORTER_QUERYING_TYPE) {
				xpath += "("
						+ processCedentShorter(consequentSetting, dbaSettings, bbaSettings, "Consequent", 0,
								0) + ")";
			}
			if (conditionSetting != null && conditionSetting.isEmpty() == false) {
				xpath += " and ";
			}
		}
		if (conditionSetting != null && conditionSetting.isEmpty() == false) {
			if (SHORTER_QUERYING_TYPE) {
				xpath += "("
						+ processCedentShorter(conditionSetting, dbaSettings, bbaSettings, "Condition", 0, 0)
						+ ")";
			}
		}
		xpath = "collection(\"" + containerName + "\")/PMML/AssociationRule[" + xpath + "]";
		System.out.println(xpath);
		return xpath;
	}

	/**
	 * !!! USED ONLY WHEN SHORTER_QUERYING_TYPE IS TRUE !!!
	 * 
	 * @param currentId
	 * @param dbaSettings
	 * @param bbaSettings
	 * @return
	 */
	private static String processCedentShorter(String currentId, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings, String cedentName, int step, int queryType) {
		Set<String> relatedBaRefs = new HashSet<String>();
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

		System.out.println("STEP: " + step);
		System.out.println("CEDENT: " + cedentName);

		int loopCount = 0;
		for (String baRef : relatedBaRefs) {
			if (relatedBaRefs.size() == 1) {
				if (step == 0) {
					if (loopCount > 0) {
						xpath.append(" or ");
					}
					xpath.append("(");
					xpath.append("count(" + cedentName);
					xpath.append("/DBA");
					xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, ++step, 1));
					xpath.append(") > 0");
					xpath.append(" and count(" + cedentName);
					xpath.append("/DBA");
					xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, step, 2));
					xpath.append(") = 0");
					xpath.append(" and count(" + cedentName);
					xpath.append("/DBA");
					xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, step, 3));
					xpath.append(") = 0");
					xpath.append(")");
				} else {
					xpath.append("/DBA");
					xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, ++step,
							queryType));
				}
			} else if (relatedBaRefs.size() > 1) {
				xpath.append("[DBA");
				xpath.append(processCedentShorter(baRef, dbaSettings, bbaSettings, cedentName, ++step,
						queryType));
				xpath.append("]");
			}
		}

		return xpath.toString();
	}

	private static StringBuffer processBbas(String currentId, Set<BbaSetting> bbaSettings,
			StringBuffer xpath, int queryType) {
		for (BbaSetting bbaSetting : bbaSettings) {
			if (bbaSetting.getId().equals(currentId)) {

				if (bbaSetting.getFieldRef() != null) {
					String dictionary = bbaSetting.getFieldRef().getDictionary();
					xpath.append("/BBA/" + dictionary + "[");
				} else {
					continue;
				}

				// TODO set value some other way

				if (queryType < 3) {
					xpath.append("FieldName = \"" + bbaSetting.getFieldRef().getValue() + "\" and (");

					Coefficient coefficient = bbaSetting.getCoefficient();
					if (coefficient.getCategories() != null) {
						String connective = "and";
						if (coefficient.getType().equals("At least one from listed")) {
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
						}
					}
					xpath.append(")");
				} else {
					xpath.append("FieldName != \"" + bbaSetting.getFieldRef().getValue() + "\"");
				}
				xpath.append("]");
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
	private static String processCedent(String currentId, Set<DbaSetting> dbaSettings,
			Set<BbaSetting> bbaSettings) {
		Set<String> relatedBaRefs = new HashSet<String>();
		StringBuffer xpath = new StringBuffer();

		for (DbaSetting dbaSetting : dbaSettings) {
			if (dbaSetting.getId().equals(currentId)) {
				for (String baRef : dbaSetting.getBaSettingRefs()) {
					relatedBaRefs.add(baRef);
					// dbaSettings.remove(dbaSetting);
				}
			}
		}

		if (relatedBaRefs.size() == 0) {
			for (BbaSetting bbaSetting : bbaSettings) {
				if (bbaSetting.getId().equals(currentId)) {

					if (bbaSetting.getFieldRef() != null) {
						String dictionary = bbaSetting.getFieldRef().getDictionary();
						xpath.append("/BBA/" + dictionary + "[");
					} else {
						continue;
					}

					// TODO set value some other way

					xpath.append("FieldName = \"" + bbaSetting.getFieldRef().getValue() + "\" and (");

					Coefficient coefficient = bbaSetting.getCoefficient();
					if (coefficient.getCategories() != null) {
						String connective = "and";
						if (coefficient.getType().equals("At least one from listed")) {
							connective = "or";
						}
						int i = 0;
						for (String category : coefficient.getCategories()) {
							if (i > 0) {
								xpath.append(" " + connective + " ");
							}
							xpath.append("CatName = \"" + category + "\"");
						}
					}
					xpath.append(")]");
				}
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
