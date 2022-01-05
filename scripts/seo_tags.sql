SELECT
  uri,
  c.id,
  pagetitle,
  longtitle,
  description,
  alias,
  parent,
  isfolder,
  context_key,
  content_type,
  var_Keywords.value as var_Keywords,
  var_seoDescription.value as var_seoDescription,
  var_seokeywords.value as var_seokeywords
FROM modx_site_content c
  LEFT OUTER JOIN modx_site_tmplvar_contentvalues var_Keywords ON var_Keywords.contentid = c.id AND var_Keywords.tmplvarid = 34
  LEFT OUTER JOIN modx_site_tmplvar_contentvalues var_seoDescription ON var_seoDescription.contentid = c.id AND var_seoDescription.tmplvarid = 58
  LEFT OUTER JOIN modx_site_tmplvar_contentvalues var_seokeywords ON var_seokeywords.contentid = c.id AND var_seokeywords.tmplvarid = 59;

# Var ids
# Keywords 34
# seoDescription 58
# seokeywords 59

