package izi_repository.transformation;

import izi_repository.domain.output.AssociationRule;
import izi_repository.domain.output.DBA;
import izi_repository.domain.output.Hit;
import izi_repository.domain.result.BBA;
import izi_repository.domain.result.Result;
import izi_repository.domain.result.Rule;
import izi_repository.domain.result.TransformationDictionary;

import java.util.List;


/**
 * Class for converting objects representing query result to output objects.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultForOutputObjectConverter {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ResultForOutputObjectConverter() {
	}

	/**
	 * Converts {@link Result} to {@link Hit}. Does not set AssociationRule
	 * field.
	 * 
	 * @param result
	 * @return
	 */
	public static Hit convert(Result result) {
		Hit hit = new Hit();

		if (result != null) {
			hit.setRuleId(result.getRuleId());
			hit.setDocId(result.getDocId());
			hit.setDocName(result.getDocName());
			hit.setDatabase(result.getDatabase());
			hit.setReportUri(result.getReportUri());
			hit.setQueryCompliance(result.getQueryCompliance());
		}

		return hit;
	}

	/**
	 * Converts {@link Rule} to {@link AssociationRule}. Sets given ids.
	 * 
	 * @param rule
	 * @return
	 */
	public static AssociationRule convert(Rule rule, String antecedentId, String consequentId, String conditionId) {
		AssociationRule ar = new AssociationRule();

		if (rule != null) {
			if (rule.getAnnotation() != null) {
				ar.setInterestingness(rule.getAnnotation().getInterestingness());
			}
			ar.getImValues().addAll(rule.getImValues());
		}

		if (antecedentId != null) {
			ar.setAntecedent(antecedentId);
		}
		if (consequentId != null) {
			ar.setConsequent(consequentId);
		}
		if (conditionId != null) {
			ar.setCondition(conditionId);
		}

		return ar;
	}

	/**
	 * Creates new {@link DBA}. Sets given id and baRefs.
	 * 
	 * @param dbaConnective
	 * @param dbaId
	 * @param baRefs
	 * @return
	 */
	public static DBA convert(String dbaConnective, String dbaId, List<String> baRefs) {
		DBA dbaOut = new DBA();

		dbaOut.setId(dbaId);
		dbaOut.setConnective(dbaConnective);
		dbaOut.getBaRefs().addAll(baRefs);

		return dbaOut;
	}

	/**
	 * Converts {@link BBA} to {@link izi_repository.domain.output.BBA}. Sets
	 * given id.
	 * 
	 * @param bba
	 * @param id
	 * @return
	 */
	public static izi_repository.domain.output.BBA convert(BBA bba, String id) {
		izi_repository.domain.output.BBA bbaOut = new izi_repository.domain.output.BBA();

		if (bba != null) {
			bbaOut.setText("");
			TransformationDictionary transDictionary = bba.getTransformationDictionary();
			if (transDictionary != null) {
				bbaOut.setFieldRef(transDictionary.getFieldName());
				bbaOut.getCatRefs().addAll(transDictionary.getCatNames());
			}
		}

		bba.setId(id);

		return bbaOut;
	}
}
