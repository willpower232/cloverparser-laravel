<svg xmlns="http://www.w3.org/2000/svg" width="133" height="20">
    <mask id="a">
        <rect width="133" height="20" rx="3" fill="#fff" />
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h95v20H0z" />
        <path fill="hsl({{ $percentage }},50%,50%)" d="M95 0h38v20H95z" />
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="47" y="15" fill="#010101" fill-opacity=".3">code coverage</text>
        <text x="47" y="14">code coverage</text>
        <text x="113" y="15" fill="#010101" fill-opacity=".3">{{ $percentage }}%</text>
        <text x="113" y="14">{{ $percentage }}%</text>
    </g>
</svg>
