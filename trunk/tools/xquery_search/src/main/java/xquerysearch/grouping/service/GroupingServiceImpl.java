package xquerysearch.grouping.service;

import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.querysettings.GroupingType;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.grouping.GroupDescription;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Result;
import xquerysearch.service.QueryService;
import xquerysearch.utils.GroupUtils;
import xquerysearch.utils.ResultUtils;

/**
 * Implementation of {@link GroupingService}.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupingServiceImpl implements GroupingService {

	@Autowired
	private QueryService queryService;
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Group> getGroupsByQuery(ArBuilderQuery query, QuerySettings settings) {
		List<Result> results = queryService.getResultList(query, settings);
		return groupBy(results, settings.getParams());
	}
	
	/**
	 * @{inheritDoc
	 */
	@Override
	public List<Group> groupBy(List<Result> results, Params params) {
		if (results == null || params == null) {
			return null;
		}

		String groupBy = params.getGroupBy();
		
		GroupingType type = GroupingType.convert(groupBy);
		
		if (type != null) {
			switch (type) {
				case CATEGORY : return groupByCategory(results, params.getFieldRef());
				case FIELDREF : return groupByFieldRef(results);
				case FIELDREF_BY_CEDENT : return groupByCedentFieldRef(results);
				case RULE_LENGTH : return groupByRuleLength(results);
				case RULE_LENGTH_BY_CEDENT : return groupByCedentLength(results);
				default : return null;
			}
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
			List<BBA> antecedentBbas = new ArrayList<BBA>();
			List<BBA> consequentBbas = new ArrayList<BBA>();
			List<BBA> conditionBbas = new ArrayList<BBA>();
			
			if (result.getRule() != null) {
				antecedentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getAntecedent()));
				consequentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getConsequent()));
				conditionBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getCondition()));
			}
			
			int antecedentLength = antecedentBbas.size();
			int consequentLength = consequentBbas.size();
			int conditionLength = conditionBbas.size();
			
			Group group = GroupUtils.getGroupByCedentsLength(groups, antecedentLength, consequentLength, conditionLength);
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				
				GroupDescription newDescription = new GroupDescription();
				newDescription.setAntecedentLength(antecedentLength);
				newDescription.setConsequentLength(consequentLength);
				newDescription.setConditionLength(conditionLength);
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
	private List<Group> groupByCedentFieldRef(List<Result> results) {
		if (results == null) {
			return null;
		}

		List<Group> groups = new ArrayList<Group>();
		
		for (Result result : results) {
			List<BBA> antecedentBbas = new ArrayList<BBA>();
			List<BBA> consequentBbas = new ArrayList<BBA>();
			List<BBA> conditionBbas = new ArrayList<BBA>();
			
			if (result.getRule() != null) {
				antecedentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getAntecedent()));
				consequentBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getConsequent()));
				conditionBbas = new ArrayList<BBA>(ResultUtils.getBbasFromCedent(result.getRule().getCondition()));
			}
			
			List<String> antecedentFieldRefs = ResultUtils.getAllFieldRefsFromBbas(antecedentBbas);
			List<String> consequentFieldRefs = ResultUtils.getAllFieldRefsFromBbas(consequentBbas);
			List<String> conditionFieldRefs = ResultUtils.getAllFieldRefsFromBbas(conditionBbas);
			
			Group group = GroupUtils.getGroupByCedentFieldRef(groups, antecedentFieldRefs, consequentFieldRefs, conditionFieldRefs);
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				
				GroupDescription newDescription = new GroupDescription();
				newDescription.getAntecedentFieldRefs().addAll(antecedentFieldRefs);
				newDescription.getConsequentFieldRefs().addAll(consequentFieldRefs);
				newDescription.getConditionFieldRefs().addAll(conditionFieldRefs);
				newGroup.setDescription(newDescription);

				groups.add(newGroup);
			} else {
				group.getResults().add(result);
			}
		}
		return groups;
	}
}
