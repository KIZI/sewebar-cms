package xquerysearch.analysis;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.analysis.ResultAnalysisOutput;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Rule;
import xquerysearch.domain.result.datadescription.DataField;

/**
 * Analyzer for {@link AssociationRuleInternal}.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRuleAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private AssociationRuleAnalyzer() {
	}

	/**
	 * Analyzes {@link Rule} represented by {@link AssociationRuleInternal}.
	 * 
	 * @param ari
	 * @return {@link ResultAnalysisOutput} describing {@link Rule}
	 */
	public static ResultAnalysisOutput analyze(AssociationRuleInternal ari) {
		ResultAnalysisOutput output = new ResultAnalysisOutput();

		output.setAntecedentBbaCount(ari.getAntecedentBbas().size());
		output.setConsequentBbaCount(ari.getConsequentBbas().size());
		output.setConditionBbaCount(ari.getConditionBbas().size());

		Map<String, Double> concretenessMap = new HashMap<String, Double>();
		concretenessMap.putAll(analyzeConcreteness(ari.getAntecedentBbas(), ari.getDataDescription().getDataFields()));
		concretenessMap.putAll(analyzeConcreteness(ari.getConsequentBbas(), ari.getDataDescription().getDataFields()));
		concretenessMap.putAll(analyzeConcreteness(ari.getConditionBbas(), ari.getDataDescription().getDataFields()));
		
		output.getConcretenessMap().putAll(concretenessMap);
		
		return output;
	}

	private static Map<String, Double> analyzeConcreteness(List<BBA> bbas, List<DataField> fields) {
		Map<String, Double> ret = new HashMap<String, Double>();
		for (BBA bba : bbas) {
			int categoryCount = bba.getTransformationDictionary().getCatNames().size();
			int fieldCategoryCount = getCategoryCountForField(bba.getTransformationDictionary().getFieldName(), fields);
			ret.put(bba.getTransformationDictionary().getFieldName(), new Double(categoryCount / fieldCategoryCount));
		}
		return ret;
	}

	private static int getCategoryCountForField(String name, List<DataField> fields) {
		for (DataField field : fields) {
			if (field.getName().equals(name)) {
				return field.getCategories().size();
			}
		}
		return 0;
	}
}
