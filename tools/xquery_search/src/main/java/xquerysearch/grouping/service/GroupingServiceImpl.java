package xquerysearch.grouping.service;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.querysettings.GroupingType;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.grouping.GroupDescription;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Result;
import xquerysearch.utils.GroupUtils;
import xquerysearch.utils.ResultUtils;

/**
 * Implementation of {@link GroupingService}.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupingServiceImpl implements GroupingService {

	/**
	 * @{inheritDoc
	 */
	@Override
	public List<Group> groupBy(List<Result> results, Params params) {
		if (results == null || params == null) {
			return null;
		}

		String groupBy = params.getGroupBy();

		if (groupBy.equals(GroupingType.CATEGORY.getText())) {
			return groupByCategory(results, params.getFieldRef());
		} else if (groupBy.equals(GroupingType.FIELDREF.getText())) {
			return groupByFieldRef(results);
		} else if (groupBy.equals(GroupingType.RULE_LENGTH.getText())) {
			return groupByRuleLength(results);
		}
		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param results
	 * @return
	 */
	private List<Group> groupByFieldRef(List<Result> results) {
		if (results == null) {
			return null;
		}
		List<Group> groups = new ArrayList<Group>();
		for (Result result : results) {
			List<BBA> bbas = ResultUtils.getBbasFromResult(result);
			List<String> fieldRefs = ResultUtils.getAllFieldRefsFromBbas(bbas);
			Group group = GroupUtils.getGroupByFieldRef(groups, fieldRefs);
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				GroupDescription newDescription = new GroupDescription();
				newDescription.getFieldRefs().addAll(fieldRefs);
				newGroup.setDescription(newDescription);

				groups.add(newGroup);
			} else {
				group.getResults().add(result);
			}
		}
		return groups;
	}

	/**
	 * TODO documentation
	 * 
	 * @param results
	 * @param fieldRef
	 * @return
	 */
	private List<Group> groupByCategory(List<Result> results, String fieldRef) {
		if (results == null || fieldRef == null) {
			return null;
		}

		List<Group> groups = new ArrayList<Group>();

		for (Result result : results) {
			List<BBA> bbas = ResultUtils.getBbasFromResult(result);
			List<String> categoryNames = ResultUtils.getCategoriesFromBbasByFieldRef(bbas, fieldRef);
			Group group = GroupUtils.getGroupByCategories(groups, categoryNames, fieldRef);
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				GroupDescription newDescription = new GroupDescription();
				newDescription.getCategories().addAll(categoryNames);
				newDescription.setFieldRefName(fieldRef);
				newGroup.setDescription(newDescription);

				groups.add(newGroup);
			} else {
				group.getResults().add(result);
			}
		}

		return groups;
	}

	/**
	 * TODO documentation
	 * 
	 * @param results
	 * @return
	 */
	private List<Group> groupByRuleLength(List<Result> results) {
		if (results == null) {
			return null;
		}

		List<Group> groups = new ArrayList<Group>();

		for (Result result : results) {
			List<BBA> bbas = ResultUtils.getBbasFromResult(result);
			Group group = GroupUtils.getGroupByRuleLength(groups, bbas.size());
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				GroupDescription newDescription = new GroupDescription();
				newDescription.setRuleLength(bbas.size());
				newGroup.setDescription(newDescription);

				groups.add(newGroup);
			} else {
				group.getResults().add(result);
			}
		}

		return groups;
	}
	
	/**
	 * TODO documentation
	 * 
	 * @param results
	 * @return
	 */
	private List<Group> groupByCedentLength(List<Result> results) {
		if (results == null) {
			return null;
		}

		List<Group> groups = new ArrayList<Group>();
		
		for (Result result : results) {
			List<BBA> antecedentBbas = null;
			List<BBA> consequentBbas = null;
			List<BBA> conditionBbas = null;
			
			if (result.getRule() != null) {
				antecedentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getAntecedent()));
				consequentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getConsequent()));
				conditionBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getCondition()));
			}
		}
		return groups;
	}

}
