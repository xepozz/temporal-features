package cmd

import "testing"

func TestNormalizeSDKVersion(t *testing.T) {
	for _, tc := range []struct {
		name     string
		input    string
		expected string
	}{
		{"registry version without prefix", "1.48.0", "v1.48.0"},
		{"version already prefixed", "v1.48.0", "v1.48.0"},
		{"empty", "", ""},
		{"relative path", "../sdk-go", "../sdk-go"},
		{"absolute path", "/home/runner/sdk-go", "/home/runner/sdk-go"},
	} {
		t.Run(tc.name, func(t *testing.T) {
			if actual := NormalizeSDKVersion(tc.input); actual != tc.expected {
				t.Fatalf("NormalizeSDKVersion(%q) = %q, expected %q", tc.input, actual, tc.expected)
			}
		})
	}
}

func TestGoBuildTagsAcceptsUnprefixedVersion(t *testing.T) {
	if tags := GoBuildTags("1.10.0"); len(tags) != 2 {
		t.Fatalf("GoBuildTags(\"1.10.0\") = %v, expected both pre-version tags", tags)
	}
	if tags := GoBuildTags("../sdk-go"); tags != nil {
		t.Fatalf("GoBuildTags(\"../sdk-go\") = %v, expected no tags", tags)
	}
}
