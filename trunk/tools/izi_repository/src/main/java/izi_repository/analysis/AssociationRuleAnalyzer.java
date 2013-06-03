package izi_repository.analysis;

import izi_repository.domain.AssociationRuleInternal;
import izi_repository.domain.analysis.ResultAnalysisOutput;
import izi_repository.domain.result.BBAForAnalysis;
import izi_repository.domain.result.Rule;
import izi_repository.domain.result.datadescription.DataField;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;


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

		List<DataField> fields = new ArrayList<DataField>();
		if (ari.getDataDescription() != null && ari.getDataDescription().getDataFields() != null) {
			fields = ari.getDataDescription().getDataFields();
		}

		concretenessMap.putAll(analyzeConcreteness(ari.getAntecedentBbas(), fields));
		concretenessMap.putAll(analyzeConcreteness(ari.getConsequentBbas(), fields));
		concretenessMap.putAll(analyzeConcreteness(ari.getConditionBbas(), fields));

		output.getConcretenessMap().putAll(concretenessMap);

		return output;
	}

	private static Map<String, Double> analyzeConcreteness(List<BBAForAnalysis> bbas, List<DataField> fields) {
		Map<String, Double> ret = new HashMap<String, Double>();
		for (BBAForAnalysis bba : bbas) {
			double categoryCount = bba.getTransformationDictionary().getCatNames().size();
			double fieldCategoryCount = getCategoryCountForField(bba.getTransformationDictionary()
					.getFieldName(), fields);
			if (categoryCount > 0 && fieldCategoryCount > 0) {
				ret.put(bba.getTransformationDictionary().getFieldName(), new Double(categoryCount / fieldCategoryCount));
			} else {
				ret.put(bba.getTransformationDictionary().getFieldName(), new Double(0));
			}
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
