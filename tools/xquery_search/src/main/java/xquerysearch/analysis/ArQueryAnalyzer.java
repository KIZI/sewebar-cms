package xquerysearch.analysis;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.analysis.ArQueryAnalysisOutput;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.datadescription.DataDescription;
import xquerysearch.utils.DataDescriptionUtils;

/**
 * Analyzer for {@link ArQuery}.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ArQueryAnalyzer() {
	}

	/**
	 * Analyzes {@link ArBuilderQuery}.
	 * 
	 * @param aqi
	 * @return {@link ArQueryAnalysisOutput} describing {@link ArBuilderQuery}
	 */
	public static ArQueryAnalysisOutput analyze(ArQueryInternal aqi) {
		ArQueryAnalysisOutput output = new ArQueryAnalysisOutput();

		output.setAntecedentBbaCount(aqi.getAntecedentBbaSettingList().size());
		output.setConsequentBbaCount(aqi.getConsequentBbaSettingList().size());
		output.setConditionBbaCount(aqi.getConditionBbaSettingList().size());

		Map<String, Double> concretenessMap = new HashMap<String, Double>();
		concretenessMap.putAll(analyzeConcreteness(aqi.getAntecedentBbaSettingList(),
				aqi.getDataDescription()));
		concretenessMap.putAll(analyzeConcreteness(aqi.getConsequentBbaSettingList(),
				aqi.getDataDescription()));
		concretenessMap
				.putAll(analyzeConcreteness(aqi.getConditionBbaSettingList(), aqi.getDataDescription()));

		output.getConcretenessMap().putAll(concretenessMap);

		return output;
	}

	private static Map<String, Double> analyzeConcreteness(List<BbaSetting> bbas, DataDescription dataDescription) {
		Map<String, Double> ret = new HashMap<String, Double>();
		for (BbaSetting bbaSetting : bbas) {
			int categoryCount = bbaSetting.getCoefficient().getCategories().size();
			int fieldCategoryCount = DataDescriptionUtils.getCategories(dataDescription,
					bbaSetting.getFieldRef().getValue()).size();
			ret.put(bbaSetting.getFieldRef().getValue(), new Double(categoryCount / fieldCategoryCount));
		}
		return ret;
	}

}
